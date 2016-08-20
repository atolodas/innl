<?php

class Custom_Lancaster_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getAttributeSetsLettert() {
        return array(
            25 => 'a',
            29 => 'b',
            30 => 'c',
            31 => 'd',
            32 => 'e',
            33 => 'f',
            34 => 'g'
        );
    }

    public function getPermissions($type,$setId,$groups) {
        // echo '?'.$setId.'?'.$groups.'?';
        $roleId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $role = Mage::getSingleton('customer/group')->load($roleId)->getData('customer_group_code');
        if($setId) {
            switch($setId) {
                case 20: // Users group

                    switch ($type) {
                        case 'delete':
                        case 'edit':
                        case 'add':
                        case 'global':
                            switch ($roleId) {
                                case 3: // Nurse Senior Admin
                                    return true;
                                    break;
                                case 2: // Nurse Admin
                                case 5: // Nurse
                                default:
                                    return false;
                                    break;
                            }
                            break;
                        case 'view':
                        case 'assign':
                        default:
                            return false;
                            break;

                    }
                    break;
                case 23: // School
                    switch ($type) {
                        case 'delete':
                        case 'add':
                        case 'edit':
                            switch ($roleId) {
                                case 3: // Nurse Senior Admin
                                    return true;
                                    break;
                                case 2: // Nurse Admin
                                case 5: // Nurse
                                default:
                                    return false;
                                    break;
                            }
                            break;
                        case 'global':
                            return true;
                            break;

                        case 'view':
                            switch ($roleId) {
                                case 3: // Nurse Senior Admin
                                    return true;
                                    break;
                                case 2: // Nurse Admin
                                    return true;
                                    break;
                                case 5: // Nurse
                                    return true;
                                    break;
                                default:
                                    return false;
                                    break;
                            }
                            break;
                        case 'assign':

                        default:
                            return false;
                            break;

                    }
                    break;
                case 21: // Region
                case 22: // Location
                switch ($type) {
                    case 'add':
                    case 'delete':
                    case 'edit':
                    case 'global':
                        switch ($roleId) {
                            case 7:
                                return true;
                                break;
                            case 3: // Nurse Senior Admin
                            case 2: // Nurse Admin
                            case 5: // Nurse
                            default:
                                return false;
                                break;
                        }
                        break;
                    case 'view':
                    case 'assign':
                    default:
                        return false;
                        break;

                }
                    break;
                case 24: // Appointsment
                    switch ($type) {
                        case 'view':
                        case 'global':
                            return true;
                        case 'delete':
                        case 'edit':
                        case 'add':
                        case 'assign':
                        default:
                            return false;
                            break;

                    }
                    break;
                    break;
                default:
                    break;
            }
        } elseif($groups) {

            $groups = explode(',',$groups);

            if(count($groups)>2) {
                switch ($type) {
                    case 'add':
                    case 'delete':
                    case 'edit':
                    case 'global':
                        switch ($roleId) {
                            case 7:
                                return true;
                                break;
                            case 3: // Nurse Senior Admin
                            case 2: // Nurse Admin
                            case 5: // Nurse
                            default:
                                return false;
                                break;
                        }
                        break;
                    case 'view':
                    case 'assign':
                    default:
                        return false;
                        break;

                }
            } else {

                $groups = $groups[0];
            switch($groups) {
                case 'Nurse (Grade 6)':
                    switch ($type) {
                        case 'view':
                        case 'assign':
                        case 'global':
                            switch ($roleId) {
                                case 3: // Nurse Senior Admin
                                    return true;
                                    break;
                                case 2: // Nurse Admin
                                case 5: // Nurse
                                default:
                                    return false;
                                    break;
                            }
                            break;

                        case 'add':
                        case 'delete':
                        case 'edit':
                        default:
                            return false;
                            break;

                    }
                    break;
                case 'Pupil':
                    switch ($type) {
                        case 'view':
                        case 'global':
                            return true;
                            break;
                        case 'assign':
                            return false;
                        case 'edit':
                            switch ($roleId) {
                                case 3: // Nurse Senior Admin
                                    return true;
                                    break;
                                case 2: // Nurse Admin
                                case 5: // Nurse
                                default:
                                    return false;
                                    break;
                            }
                            break;
                        case 'add':
                        case 'delete':
                            switch ($roleId) {
                                case 3: // Nurse Senior Admin
                                    return true;
                                    break;
                                case 2: // Nurse Admin
                                case 5: // Nurse
                                default:
                                    return false;
                                    break;
                            }
                            break;

                        default:
                            return false;
                            break;

                    }
                    break;
                default:
                    break;
            }
            }
        } else {
            return true;
        }



    }
}