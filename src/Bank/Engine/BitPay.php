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
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *        
 */
class Bank_Engine_BitPay extends Bank_Engine
{

    const ClientID = 'ClientID';

    const ClientSecret = 'ClientSecret';

    const Currency = 'Currency';

    var $client = false;

    /*
     *
     */
    public function getTitle()
    {
        return 'BitPay';
    }

    /*
     *
     */
    public function getDescription()
    {
        return 'Accept payments with one of BitPay\'s';
    }
    
    /*
     *
     */
    public function getCurrency()
    {
        return 'USD';
    }

    /*
     *
     */
    public function getExtraParam()
    {
        return array(
            array(
                'name' => ClientID,
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
                'name' => ClientSecret,
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
//             array(
//                 'name' => Currency,
//                 'type' => 'String',
//                 'unit' => 'none',
//                 'title' => 'Currency',
//                 'description' => 'Currency',
//                 'editable' => true,
//                 'visible' => true,
//                 'priority' => 1,
//                 'defaultValue' => 'USD',
//                 'validators' => [
//                     'NotNull',
//                     'NotEmpty'
//                 ]
//             )
        );
    }

    /*
     */
    public function create($receipt)
    {
        // https://github.com/paypal/PayPal-PHP-SDK/wiki/Making-First-Call
        // Redirect to URL You can do it also by creating a form
        throw new Bank_Exception('Not supported');
    }

    /**
     */
    public function update($receipt)
    {
        // https://github.com/paypal/PayPal-PHP-SDK/wiki/Making-First-Call
        // Redirect to URL You can do it also by creating a form
        throw new Bank_Exception('Not supported');
    }
}