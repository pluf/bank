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
class Bank_Views_Wallet extends Pluf_Views
{

    /**
     * Create new wallet
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function create($request, $match)
    {
        $wallet = new Bank_Wallet();
        $wallet->_a['cols']['currency']['editable'] = true;
        $wallet->_a['cols']['owner_id']['editable'] = true;
        $request->REQUEST['owner_id'] = $request->user->id;
        Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
        $form = Pluf_Shortcuts_GetFormForModel($wallet, $request->REQUEST);
        $wallet = $form->save();
        return $wallet;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function find($request, $match)
    {
        $p = array(
            'model' => 'Bank_Wallet'
        );
        if (!User_Precondition::isOwner($request)) {
            $where = new Pluf_SQL('`owner_id`=%s', array(
                $request->user->id
            ));
            $p['sql'] = $where;
        }
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
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['modelId']);
        if ($request->user->getId() !== $wallet->owner_id && ! User_Precondition::isOwner($request)) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        return $wallet;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function delete($request, $match)
    {
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['modelId']);
        if ($request->user->getId() !== $wallet->owner_id) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        return parent::deleteObject($request, $match, array(
            'model' => 'Bank_Wallet',
            'permanently' => false
        ));
    }

    /**
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public function update($request, $match)
    {
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $wallet = Pluf_Shortcuts_GetObjectOr404('Bank_Wallet', $match['modelId']);
        if ($request->user->getId() !== $wallet->owner_id) {
            throw new Pluf_Exception_PermissionDenied("Permission is denied");
        }
        return parent::updateObject($request, $match, array(
            'model' => 'Bank_Wallet'
        ));
    }

}


