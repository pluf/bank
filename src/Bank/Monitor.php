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
 * Monitors of bank module
 * 
 * @author hadi
 *
 */
class Bank_Monitor
{

    /**
     * Total amount of all paid receipts
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return number
     */
    public static function paidAmount($request, $match)
    {
        $model = new Bank_Receipt();
        $params = array(
            'select' => 'SUM(amount) AS amount',
            'filter' => array(
                'payRef IS NOT NULL',
                'payMeta IS NOT NULL'
            )
        );
        $result = $model->getList($params);
        return $result[0]->amount;
    }

}

