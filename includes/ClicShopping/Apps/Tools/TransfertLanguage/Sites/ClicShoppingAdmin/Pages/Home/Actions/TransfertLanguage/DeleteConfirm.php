<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions\TransfertLanguage;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {

    protected mixed $app;
    protected $transfert_directory;

    public function __construct()
    {
      $this->app = Registry::get('TransfertLanguage');
      $this->transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (strstr($_GET['file'], '..')) $this->app->redirect('TransferLanguage');

      if (unlink($this->transfert_directory . '/' . $_GET['file'])) {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_file_deleted'), 'success');

        $this->app->redirect('TransfertLanguage');
      }
    }
  }