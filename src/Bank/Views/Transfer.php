<?php

/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class Bank_Views_Transfer extends Pluf_Views
{

    /**
     * Create new transfer
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_BadRequest
     * @throws Pluf_Exception_PermissionDenied
     * @throws Pluf_Exception_DoesNotExist
     * @return Bank_Transfer
     */
    public function create($request, $match)
    {
        // Check amount
        $amount = $request->REQUEST['amount'];
        if ($amount <= 0.0) {
            throw new Pluf_Exception_BadRequest('Invalid amount. Amount should be positive value.');
        }
        // Check wallets and permissions to access wallets
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $fromWallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['parentId']);
        if ($request->user->getId() !== $fromWallet->owner_id) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        if ($fromWallet->deleted) {
            throw new Pluf_Exception_DoesNotExist("Source wallet is deleted!");
        }
        $toWallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $request->REQUEST['to_wallet_id']);
        if ($toWallet->deleted) {
            throw new Pluf_Exception_DoesNotExist("Destination wallet is deleted!");
        }
        // Check for invalid transfer
        if ($fromWallet->id === $toWallet->id) {
            throw new Pluf_Exception_BadRequest('Invalid transfer. Source and destination wallet of transfer could not be same.');
        }
        // Check currency of wallets
        Pluf::loadFunction('Bank_Shortcuts_IsCurrenciesCompatible');
        if ($fromWallet->currency !== $toWallet->currency) {
            throw new Pluf_Exception_BadRequest('Invalid transfer. Could not transfer between wallets with different currency.');
        }
        // Check if balance of source wallet is sufficeint
        $sourceBalance = $fromWallet->total_deposit - $fromWallet->total_withdraw;
        if ($sourceBalance < $amount) {
            throw new Pluf_Exception_BadRequest('Insufficeint balance [balance: ' . $sourceBalance . ', transfer: ' . $amount . '].');
        }
        // Create transfer
        $transfer = new Bank_Transfer();
        $transfer->_a['cols']['amount']['editable'] = true;
        $transfer->_a['cols']['acting_id']['editable'] = true;
        $transfer->_a['cols']['from_wallet_id']['editable'] = true;
        $transfer->_a['cols']['to_wallet_id']['editable'] = true;
        Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
        $data = array_merge($request->REQUEST, array(
            'acting_id' => $request->user->id,
            'from_wallet_id' => $fromWallet->id,
            'to_wallet_id' => $toWallet->id
        ));
        $form = Pluf_Shortcuts_GetFormForModel($transfer, $data);
        $transfer = $form->save();
        // Update balance of wallets
        $fromWallet->total_withdraw += $transfer->amount;
        $fromWallet->update();
        $toWallet->total_deposit += $transfer->amount;
        $toWallet->update();
        return $transfer;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function find($request, $match)
    {
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['parentId']);
        if ($request->user->getId() !== $wallet->owner_id && ! User_Precondition::isOwner($request)) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        $where = new Pluf_SQL('`from_wallet_id`=%s OR `to_wallet_id`=%s', array(
            $wallet->id,
            $wallet->id
        ));
        $p = array(
            'model' => 'Bank_Transfer',
            'sql' => $where
        );
        return parent::findObject($request, $match, $p);
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function get($request, $match)
    {
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['parentId']);
        if ($request->user->getId() !== $wallet->owner_id && ! User_Precondition::isOwner($request)) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        $transfer = Pluf_Shortcuts_GetObjectOr404('Bank_Transfer', $match['modelId']);
        if ($transfer->from_wallet_id !== $wallet->id && $transfer->to_wallet_id !== $wallet->id) {
            throw new Pluf_Exception_DoesNotExist('The transfer is not blong to the wallet.');
        }
        return $transfer;
    }

    /**
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_BadRequest
     * @throws Pluf_Exception_DoesNotExist
     * @return Bank_Receipt
     */  
    public function createPayment($request, $match)
    {
        // Check wallet and permissions to access wallet
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $toWallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['parentId']);
        if ($toWallet->deleted) {
            throw new Pluf_Exception_DoesNotExist("Destination wallet is deleted!");
        }
        // Check amount
        $amount = $request->REQUEST['amount'];
        if ($amount <= 0.0) {
            throw new Pluf_Exception_BadRequest('Invalid amount. Amount should be a positive value.');
        }
        // Check bank backend and currencies
        $backend = Pluf_Shortcuts_GetObjectOr404('Bank_Backend', $request->REQUEST['backend']);
        Pluf::loadFunction('Bank_Shortcuts_IsCurrenciesCompatible');
        if (!Bank_Shortcuts_IsCurrenciesCompatible($backend->currency, $toWallet->currency)) {
            throw new Pluf_Exception_BadRequest('Invalid payment. Could not transfer between bank backend and wallet with different currency.');
        }
        $title = array_key_exists('title', $request->REQUEST) ? $request->REQUEST['title'] : 'Charge wallet ' . $toWallet->id;
        $description = array_key_exists('description', $request->REQUEST) ? $request->REQUEST['description'] : null;
        $email = '';
        $profiles = $request->user->get_profiles_list();
        if($profiles && $profiles->count() > 0){
            $email = $profiles[0]->public_email;
        }
        // Create payment
        Pluf::loadFunction('Bank_Shortcuts_ConvertCurrency');
        $amount = Bank_Shortcuts_ConvertCurrency($amount, $toWallet->currency, $backend->currency);
        $receiptData = array(
            // The amount is based on currency of bank backend
            'amount' => $amount,
            'title' => $title,
            'description' => $description,
            'email' => $email,
            'phone' => '',
            'callbackURL' => $request->REQUEST['callback'],
            'backend_id' => $backend->id
        );
        $payment = Bank_Service::create($receiptData, 'bank-wallet', $toWallet->id);
        return $payment;
    }
    
    /**
     * Returns the payment with given id. It also creates a transfer if payment is successfully payed.
     * 
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_PermissionDenied
     * @throws Pluf_Exception_DoesNotExist
     * @return Pluf_Model|Bank_Receipt
     */
    public function getPayment($request, $match)
    {
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        // Check wallets and permissions to access wallets
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['parentId']);
        if ($request->user->getId() !== $wallet->owner_id && ! User_Precondition::isOwner($request)) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        // Check payment
        $payment = Pluf_Shortcuts_GetObjectOr404('Bank_Receipt', $match['modelId']);
        if ($payment->owner_id !== 'bank-wallet' && $payment->owner_id !== $wallet->id) {
            throw new Pluf_Exception_DoesNotExist('The payment is not blong to the wallet.');
        }
        $preState = $payment->id >= 0 && $payment->isPayed();
        if($preState){
            return $payment;
        }
        $payment = Bank_Service::update($payment);
        $paid = $payment->isPayed();
        if (! $preState && $paid){
            // The payment is payed and it is first time that we inform about it
            // Create transfer
            $transfer = new Bank_Transfer();
            $transfer->_a['cols']['amount']['editable'] = true;
            $transfer->_a['cols']['acting_id']['editable'] = true;
            $transfer->_a['cols']['receipt_id']['editable'] = true;
            $transfer->_a['cols']['to_wallet_id']['editable'] = true;
            // Convert amount from backend currency to wallet currency
            Pluf::loadFunction('Bank_Shortcuts_ConvertCurrency');
            $amount = Bank_Shortcuts_ConvertCurrency($payment->amount, $payment->get_backend()->currency, $wallet->currency);
            Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
            $data = array(
                'amount' => $amount,
                'acting_id' => $request->user->id,
                'receipt_id' => $payment->id,
                'to_wallet_id' => $wallet->id,
                'description' => $payment->description
            );
            $form = Pluf_Shortcuts_GetFormForModel($transfer, $data);
            $transfer = $form->save();
            // Update balance of wallet
            $wallet->total_deposit += $transfer->amount;
            $wallet->update();
        }
        return $payment;
    }
    
    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function findPayments($request, $match)
    {
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['parentId']);
        if ($request->user->getId() !== $wallet->owner_id && ! User_Precondition::isOwner($request)) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        $where = new Pluf_SQL('`owner_id`=%s AND `owner_class`=%s', array(
            $wallet->id,
            'bank-wallet'
        ));
        $p = array(
            'model' => 'Bank_Receipt',
            'sql' => $where
        );
        return parent::findObject($request, $match, $p);
    }
}