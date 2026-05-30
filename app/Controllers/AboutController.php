<?php
class AboutController extends BaseController
{
    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->settingModel = new Setting($pdo);
    }

    public function index()
    {
        $rawSettings = $this->settingModel->getAll();
        $settings = array_column($rawSettings, 'setting_value', 'setting_key');

        $this->render('about/index', ['settings' => $settings], 'Về SportZone Vietnam');
    }

}

