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
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *        
 */
class Bank_Form_ReceiptNew extends Pluf_Form
{

    /*
     *
     */
    public function initFields($extra = array())
    {
        $this->fields['amount'] = new Pluf_Form_Field_Integer(array(
            'required' => true,
            'label' => 'amount'
        ));

        $this->fields['title'] = new Pluf_Form_Field_Varchar(array(
            'required' => true,
            'label' => 'title'
        ));

        $this->fields['description'] = new Pluf_Form_Field_Varchar(array(
            'required' => true,
            'label' => 'description'
        ));

        $this->fields['email'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'email'
        ));
        $this->fields['phone'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'phone'
        ));
        $this->fields['callbackURL'] = new Pluf_Form_Field_Varchar(array(
            'required' => true,
            'label' => 'callbackURL'
        ));
        $this->fields['backend_id'] = new Pluf_Form_Field_Integer(array(
            'required' => true,
            'label' => 'backend_id'
        ));
    }

    function clean_backend()
    {
        $backend = Pluf::factory(Bank_Backend::class, $this->cleaned_data['backend']);
        if ($backend->isAnonymous()) {
            throw new \Pluf\Exception('backend not found');
        }
        // XXX: maso, 1395: گرفتن پشتوانه
        return $backend->id;
    }

    /**
     *
     * @param string $commit
     * @throws \Pluf\Exception
     * @return Bank_Backend
     */
    function save($commit = true)
    {
        if (! $this->isValid()) {
            // TODO: maso, 1395: باید از خطای مدل فرم استفاده شود.
            throw new \Pluf\Exception('Cannot save a receipt from an invalid form.');
        }
        // Set attributes
        $receipt = new Bank_Receipt();
        $receipt->setFromFormData($this->cleaned_data);
        $receipt->secure_id = $this->getSecureKey();
        // موجودیت قرار گیرد.
        if ($commit) {
            if (! $receipt->create()) {
                throw new \Pluf\Exception('fail to create the recipt.');
            }
        }
        return $receipt;
    }

    /**
     * Generates new secure key
     *
     * @return string
     */
    private function getSecureKey()
    {
        $recipt = new Bank_Receipt();
        while (1) {
            $key = sha1(microtime() . rand(0, 123456789) . Pluf::f('secret_key'));
            $sess = $recipt->getList(array(
                'filter' => 'secure_id=\'' . $key . '\''
            ));
            if (count($sess) == 0) {
                break;
            }
        }
        return $key;
    }
}

