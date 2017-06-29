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
use Zarinpal\Zarinpal;

/**
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *        
 */
class Bank_Engine_Zarinpal extends Bank_Engine
{

    const MerchantID = 'MerchantID';

    var $client = false;

    /*
     *
     */
    public function getTitle ()
    {
        return 'Zarin Pal';
    }

    /*
     *
     */
    public function getDescription ()
    {
        return 'Zarin Pal Payment Service';
    }

    /*
     *
     */
    public function getExtraParam ()
    {
        return array(
                array(
                        'name' => 'MerchantID',
                        'type' => 'String',
                        'unit' => 'none',
                        'title' => 'Merchant ID',
                        'description' => 'MerchantID',
                        'editable' => true,
                        'visible' => true,
                        'priority' => 5,
                        'symbol' => 'id',
                        'defaultValue' => 'no title',
                        'validators' => [
                                'NotNull',
                                'NotEmpty'
                        ]
                )
        );
    }

    /*
     */
    public function create ($receipt)
    {
        $backend = $receipt->get_backend();
        $MerchantID = $backend->get(Bank_Engine_Zarinpal::MerchantID);
        $Authority = $receipt->getMeta('Authority', null);
        // Check
        Pluf_Assert::assertNull($Authority, 'Receipt is created before');
        Pluf_Assert::assertNotNull($backend, 'Bakend is empty');
        Pluf_Assert::assertNotNull($MerchantID, 'MerchantID is not defined');
        
        if (Pluf::f('bank_debug', false)) {
            $receipt->setMeta('Authority', 'back engine is in debug mode');
            $receipt->callURL = 'https://www.zarinpal.com/pg/StartPay/example';
            return;
        }
        
        $gate = new Zarinpal($MerchantID);
        $answer = $gate->request($receipt->callbackURL, $receipt->amount, 
                $receipt->description, $receipt->email, $receipt->phone);
        
        if (isset($answer['Authority'])) {
            file_put_contents('Authority', $answer['Authority']);
            $receipt->putMeta('Authority', $answer['Authority']);
            $receipt->callUrl = 'https://www.zarinpal.com/pg/StartPay/' .
                     $answer['Authority'];
        }
        
        // Redirect to URL You can do it also by creating a form
        throw new Bank_Exception('fail to create payment: zarinpal server erro');
    }

    /**
     */
    public function update ($receipt)
    {
        $backend = $receipt->get_backend();
        $MerchantID = $backend->get(Bank_Engine_Zarinpal::MerchantID);
        $Authority = $receipt->getMeta('Authority', null);
        // Check
        Pluf_Assert::assertNotNull($Authority, 'Receipt is created before');
        Pluf_Assert::assertNotNull($backend, 'Bakend is empty');
        Pluf_Assert::assertNotNull($MerchantID, 'MerchantID is not defined');
        
        if (Pluf::f('bank_debug', false)) {
            $receipt->payRef = 'back engine is in debug mode';
            return true;
        }
        
        // maso, 1395: تایید یک پرداخت
        // $wsdlCheck = 'Location: https://sandbox.zarinpal.com/pg/StartPay/'
        // .$result->Authority
        $client= new Zarinpal($MerchantID);
        $result = $client->verify('OK', $receipt->amount, $Authority);
        $receipt->payRef = $result->RefID;
        return true;
        
//         if ($result->Status == 100) {
//             throw new Bank_Exception_Engine('fail to check payment');
//         }
    }
}