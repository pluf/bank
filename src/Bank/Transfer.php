<?php

/**
 * Defines a wallet transfer to transfer credit from one wallet to another wallet. 
 * 
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class Bank_Transfer extends Pluf_Model
{

    /**
     * Loads data model
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'bank_transfer';
        $this->_a['verbose'] = 'Bank Transfer';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'blank' => false,
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            // It should be a positive value
            'amount' => array(
                'type' => 'Pluf_DB_Field_Float',
                'blank' => false,
                'is_null' => false,
                'default' => 0.0,
                'editable' => false,
                'readable' => true
            ),
            'description' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'is_null' => true,
                'size' => 1024,
                'editable' => true,
                'readable' => true
            ),
            'creation_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => false,
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            /*
             * Relations
             */
            'acting_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'User_Account',
                'blank' => false,
                'is_null' => false,
                'name' => 'acting',
                'graphql_name' => 'acting',
                'relate_name' => 'wallet_transfers',
                'editable' => false,
                'readable' => true
            ),
            'from_wallet_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'Bank_Wallet',
                'blank' => true,
                'is_null' => true,
                'name' => 'from_wallet',
                'graphql_name' => 'from_wallet',
                'relate_name' => 'withdrawals',
                'editable' => false,
                'readable' => true
            ),
            'to_wallet_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'Bank_Wallet',
                'blank' => false,
                'is_null' => false,
                'name' => 'to_wallet',
                'graphql_name' => 'to_wallet',
                'relate_name' => 'deposits',
                'editable' => false,
                'readable' => true
            ),
            'receipt_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'Bank_Receipt',
                'blank' => true,
                'is_null' => true,
                'name' => 'receipt',
                'graphql_name' => 'receipt',
                'relate_name' => 'transfer',
                'editable' => false,
                'readable' => true
            )
        );

        $this->_a['idx'] = array(
            'transfer_acting_idx' => array(
                'col' => 'acting_id',
                'type' => 'normal', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            ),
            'transfer_from_wallet_idx' => array(
                'col' => 'from_wallet_id',
                'type' => 'normal', // normal, unique, fulltext, spatial
            ),
            'transfer_to_wallet_idx' => array(
                'col' => 'to_wallet_id',
                'type' => 'normal', // normal, unique, fulltext, spatial
            )
        );
    }

    /**
     * \brief پیش ذخیره را انجام می‌دهد
     *
     * @param $create boolean
     *            ساخت یا به روز رسانی را تعیین می‌کند
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
    }
}