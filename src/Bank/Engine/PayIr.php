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
 * @author maso<mostafa.barmshory@dpq.co.ir>
 *        
 */
class Bank_Engine_PayIr extends Bank_Engine
{

    /**
     *
     * {@inheritdoc}
     * @see Bank_Engine::getTitle()
     */
    public function getTitle()
    {
        return 'PayIr';
    }

    /**
     *
     * {@inheritdoc}
     * @see Bank_Engine::getDescription()
     */
    public function getDescription()
    {
        return 'Pay.ir Payment Service';
    }

    /**
     *
     * {@inheritdoc}
     * @see Bank_Engine::getExtraParam()
     */
    public function getExtraParam()
    {
        return array(
            array(
                'name' => 'id',
                'type' => 'String',
                'unit' => 'none',
                'title' => 'ID',
                'description' => 'Pay.ir ID',
                'editable' => true,
                'visible' => true,
                'priority' => 5,
                'symbol' => 'pay.ir',
                'defaultValue' => '',
                'validators' => [
                    'NotNull',
                    'NotEmpty'
                ]
            )
        );
    }

    /**
     *
     * {@inheritdoc}
     * @see Bank_Engine::create($receipt)
     * @param
     *            $receipt
     */
    public function create($receipt)
    {
        // XXX:
    }

    /**
     *
     * {@inheritdoc}
     * @see Bank_Engine::update()
     */
    public function update($receipt)
    {
        // XXX:
    }
}
