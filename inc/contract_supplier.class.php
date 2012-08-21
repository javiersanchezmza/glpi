<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2012 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

// Relation between Contracts and Suppliers
class Contract_Supplier extends CommonDBRelation {

   // From CommonDBRelation
   static public $itemtype_1 = 'Contract';
   static public $items_id_1 = 'contracts_id';

   static public $itemtype_2 = 'Supplier';
   static public $items_id_2 = 'suppliers_id';


   /**
    * @param $item   string   Supplier object
   **/
   static function countForSupplier(Supplier $item) {

      $restrict = "`glpi_contracts_suppliers`.`suppliers_id` = '".$item->getField('id') ."'
                    AND `glpi_contracts_suppliers`.`contracts_id` = `glpi_contracts`.`id` ".
                    getEntitiesRestrictRequest(" AND ", "glpi_contracts", '',
                                               $_SESSION['glpiactiveentities']);

      return countElementsInTable(array('glpi_contracts_suppliers', 'glpi_contracts'), $restrict);
   }


   /**
    * @param $item   string   Contract object
   **/
   static function countForContract(Contract $item) {

      $restrict = "`glpi_contracts_suppliers`.`contracts_id` = '".$item->getField('id') ."'
                    AND `glpi_contracts_suppliers`.`suppliers_id` = `glpi_suppliers`.`id` ".
                    getEntitiesRestrictRequest(" AND ", "glpi_suppliers", '',
                                               $_SESSION['glpiactiveentities'], true);

      return countElementsInTable(array('glpi_contracts_suppliers', 'glpi_suppliers'), $restrict);
   }


   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if (!$withtemplate) {
         switch ($item->getType()) {
            case 'Supplier' :
               if (Session::haveRight("contract","r")) {
                  if ($_SESSION['glpishow_count_on_tabs']) {
                     return self::createTabEntry(Contract::getTypeName(2),
                                                 self::countForSupplier($item));
                  }
                  return Contract::getTypeName(2);
               }
               break;

            case 'Contract' :
               if (Session::haveRight("contact_enterprise","r")) {
                  if ($_SESSION['glpishow_count_on_tabs']) {
                     return self::createTabEntry(Supplier::getTypeName(2),
                                                 self::countForContract($item));
                  }
                  return Supplier::getTypeName(2);
               }
               break;
         }
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      switch ($item->getType()) {
         case 'Supplier' :
            $item->showContracts();
            break;

         case 'Contract' :
            $item->showSuppliers();
            break;
      }
      return true;
   }

}
?>
