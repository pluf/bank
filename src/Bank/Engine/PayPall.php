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
 * PayPall Engine
 *
 *
 * @see https://github.com/paypal/PayPal-PHP-SDK
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *
 */
class Bank_Engine_PayPall extends Bank_Engine
{

    const ClientID = 'ClientId';

    const ClientSecret = 'ClientSecret';

    const Currency = 'Currency';

    var $client = false;

    /*
     *
     */
    public function getTitle()
    {
        return 'PayPall';
    }

    /*
     *
     */
    public function getDescription()
    {
        return 'Accept payments with one of PayPal\'s';
    }

    public function getCurrency(){
        return 'USD';
    }

    /*
     *
     */
    public function getExtraParam()
    {
        return array(
            array(
                'name' => self::ClientID,
                'type' => 'String',
                'unit' => 'none',
                'title' => 'Clien ID',
                'description' => 'Clien ID',
                'editable' => true,
                'visible' => true,
                'priority' => 2,
                'defaultValue' => '',
                'validators' => [
                    'NotNull',
                    'NotEmpty'
                ]
            ),
            array(
                'name' => self::ClientSecret,
                'type' => 'String',
                'unit' => 'none',
                'title' => 'Clien secret',
                'description' => 'Clien secret',
                'editable' => true,
                'visible' => true,
                'priority' => 1,
                'defaultValue' => '',
                'validators' => [
                    'NotNull',
                    'NotEmpty'
                ]
            ),
            array(
                'name' => self::Currency,
                'type' => 'String',
                'unit' => 'none',
                'title' => 'Currency',
                'description' => 'Currency',
                'editable' => true,
                'visible' => true,
                'priority' => 1,
                'defaultValue' => 'USD',
                'validators' => [
                    'NotNull',
                    'NotEmpty'
                ]
            )
        );
    }

    /**
     * Create new recept
     *
     * @param Bank_Recept $receipt
     * @throws Bank_Exception
     */
    public function create($receipt)
    {

        $backend = $receipt->get_backend();
        $clientId = $backend->getMeta(self::ClientID, null);
        $clientSecret = $receipt->getMeta(self::ClientSecret, null);
        $currency = $receipt->getMeta(self::Currency, 'USD');

        // After Step 1
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $clientId,     // ClientID
                $clientSecret  // ClientSecret
            )
        );

        // After Step 2
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new \PayPal\Api\Amount();
        $amount->setTotal($receipt->amount);
        $amount->setCurrency($currency);

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount);

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls
            ->setReturnUrl($receipt->callbackURL)
            ->setCancelUrl($receipt->callbackURL);

        $payment = new \PayPal\Api\Payment();
        $payment
            ->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        // debug
        if (Pluf::f('bank_debug', false)) {
            $receipt->setMeta('paymentId', 'id');
            $receipt->setMeta('intent', 'intent');
            $receipt->callURL = 'https://www.paypall.com/pg/StartPay/example';
            return;
        }
        // After Step 3
        try {
            $response = $payment->create($apiContext);
            $receipt->setMeta('orderId', $response->result->id);
            $receipt->setMeta('intent', $response->result->intent);
            $receipt->callURL = $payment->getApprovalLink();
            return;
        } catch (Exception $ex) {
            // This will print the detailed information on the exception.
            //REALLY HELPFUL FOR DEBUGGING
            throw new Bank_Exception($ex->getData());
        }
    }

    /**
     * Update state of the Recept
     *
     * @param Bank_Recept $receipt
     * @throws Bank_Exception
     */
    public function update($receipt)
    {
        $backend = $receipt->get_backend();
        $clientId = $backend->getMeta(self::ClientID, null);
        $clientSecret = $receipt->getMeta(self::ClientSecret, null);
        $paymentId = $receipt->setMeta('paymentId', 'id');

        // After Step 1
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $clientId,     // ClientID
                $clientSecret  // ClientSecret
                )
            );

        $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

        $execution = new \PayPal\Api\PaymentExecution();
        $execution->setPayerId($paymentId);

        try {
            // Execute the payment
            $result = $payment->execute($execution, $apiContext);
            if($result->getState() === 'approved') {
                $receipt->payRef = $payment->id;
            }
            return true;
        } catch (Exception $ex) {
            throw new Bank_Exception($ex);
        }
        return false;
    }
}