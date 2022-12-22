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
  use ClicShopping\OM\FileSystem;

  class DownloadFromCrowdin extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    protected $transfert_directory;
    protected $context;
    protected $project_identifier;
    protected $project_key;

    public function __construct()
    {
      $this->app = Registry::get('TransfertLanguage');
      $this->transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
      $this->context = stream_context_create(array('http' => array('header' => 'User-Agent: ClicShopping')));
      $this->project_identifier = 'clicshopping';
      $this->project_key = '86333602f6c2067891fc1544709f4b02';
/*
      $apiKey = 'd4a680bc5c45f98d45c4cdb09d4d4270ceb35cd986aac80e7b9350252ff1a6b27ee706bb027e5231';
      $projectID = '310691';
*/
      $this->filename = 'crowdind_clicshopping_translations.zip';
    }

    public function getChekDirectory()
    {
      if (!is_dir($this->transfert_directory)) {
        if (!mkdir($concurrentDirectory = $this->transfert_directory, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        return true;
      } elseif (FileSystem::isWritable($this->transfert_directory)) {
        return true;
      } else {
        return false;
      }
    }

    public function execute()
    {
      $crowding_link = 'https://api.crowdin.com/api/project/' . $this->project_identifier . '/download/all.zip?key=' . $this->project_key;

      ini_set('auto_detect_line_endings', 1);
      ini_set('default_socket_timeout', 5); // socket timeout, just in case

      $file_xml_content = file_get_contents($crowding_link, true, $this->context);

      $headers = get_headers($crowding_link, true);

      if ($this->getChekDirectory() === true) {
        if (isset($headers['Content-Length'])) {
          file_put_contents($this->transfert_directory . 'crowdin_clicshopping_translations.zip', $file_xml_content);
        }
      }

      $this->app->redirect('TransfertLanguage');
    }
  }