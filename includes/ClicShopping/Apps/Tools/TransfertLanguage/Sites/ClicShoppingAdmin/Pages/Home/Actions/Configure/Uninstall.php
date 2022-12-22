<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Uninstall extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_TransfertLanguage = Registry::get('TransfertLanguage');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('TransfertLanguageAdminConfig' . $current_module);
      $m->uninstall();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TransfertLanguage->getDef('alert_module_uninstall_success'), 'success', 'TransfertLanguage');

      $CLICSHOPPING_TransfertLanguage->redirect('Configure&module=' . $current_module);
    }
  }