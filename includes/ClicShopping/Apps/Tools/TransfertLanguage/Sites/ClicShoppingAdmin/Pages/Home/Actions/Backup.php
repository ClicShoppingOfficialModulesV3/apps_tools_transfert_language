<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Backup extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_TransfertLanguage = Registry::get('TransfertLanguage');

      $this->page->setFile('backup.php');

      $CLICSHOPPING_TransfertLanguage->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }