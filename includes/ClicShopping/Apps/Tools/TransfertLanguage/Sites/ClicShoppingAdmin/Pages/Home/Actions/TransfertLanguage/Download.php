<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions\TransfertLanguage;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Download extends \ClicShopping\OM\PagesActionsAbstract
  {

    protected $app;
    protected $transfert_directory;

    public function __construct()
    {
      $this->app = Registry::get('TransfertLanguage');
      $this->transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $extension = substr($_GET['file'], -3);

      if (($extension == 'zip')) {
        if ($fp = fopen($this->transfert_directory . $_GET['file'], 'rb')) {
          $buffer = fread($fp, filesize($this->transfert_directory . $_GET['file']));
          fclose($fp);

          header('Content-type: application/x-octet-stream');
          header('Content-disposition: attachment; filename=' . $_GET['file']);

          echo $buffer;

          exit;
        }
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_download_link_not_acceptable'), 'error');
      }
    }
  }