<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. http://dpq.co.ir
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
Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('User_Shortcuts_UserJsonResponse');

/**
 * Manage users (CRUD on users account)
 */
class User_Views_User
{

    /**
     * Creates new user
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function create($request, $match)
    {
        // Create account
        $extra = array();
        $form = new User_Form_User(array_merge($request->REQUEST, $request->FILES), $extra);
        $cuser = $form->save();
        
        // User activation
        // $user_active = Pluf::f('user_signup_active', false);
        // $cuser->active = $user_active;
        
        // // Create profile
        // $profile_model = Pluf::f('user_profile_class', false);
        // $profile_form = Pluf::f('user_profile_form', false);
        // if ($profile_form === false || $profile_model === false) {
        // return User_Shortcuts_UserJsonResponse($cuser);
        // }
        // try {
        // $profile = $cuser->getProfile();
        // } catch (Pluf_Exception_DoesNotExist $ex) {
        // $profile = new $profile_model();
        // $profile->user = $cuser;
        // $profile->create();
        // }
        // $form = new $profile_form(array_merge($request->POST, $request->FILES),
        // array(
        // 'user_profile' => $profile
        // ));
        // $profile = $form->update();
        
        // Return response
        return User_Shortcuts_UserJsonResponse($cuser);
    }

    /**
     * Returns information of specified user by id.
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function get($request, $match)
    {
        throw new Pluf_Exception_NotImplemented();
    }

    /**
     * Updates information of specified user (by id)
     *
     * @param unknown $request            
     * @param unknown $match            
     * @return unknown
     */
    public static function update($request, $match)
    {
        // initial page data
        $extra = array(
            'user' => $request->user
        );
        $form = new User_Form_Account(array_merge($request->POST, $request->FILES), $extra);
        $cuser = $form->update();
        $request->user->setMessage(sprintf(__('Account data has been updated.'), (string) $cuser));
        
        // Return response
        return User_Shortcuts_UserJsonResponse($cuser);
    }

    /**
     * Delete specified user (by id)
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function delete($request, $match)
    {
        throw new Pluf_Exception_NotImplemented();
    }

    /**
     * Returns list of users.
     * Returned list can be customized using search fields, filters or sort fields.
     *
     * @param unknown_type $request            
     * @param unknown_type $match            
     */
    public static function find($request, $match)
    {
        $pag = new Pluf_Paginator(new Pluf_User());
        $pag->list_filters = array(
            'administrator',
            'staff',
            'active'
        );
        $search_fields = array(
            'login',
            'first_name',
            'last_name',
            'email'
        );
        $sort_fields = array(
            'id',
            'login',
            'first_name',
            'last_name',
            'date_joined',
            'last_login'
        );
        $pag->model_view = 'secure';
        $pag->configure($list_display, $search_fields, $sort_fields);
        $pag->items_per_page = $this->getListCount($request);
        $pag->setFromRequest($request);
        return new Pluf_HTTP_Response_Json($pag->render_object());
    }
}
