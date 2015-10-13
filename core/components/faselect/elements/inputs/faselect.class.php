<?php

class FASelect extends cbBaseInput {
    public $defaultIcon = 'chunk_A';
    public $defaultTpl = '<i class="fa [[+value]] [[+size]]"></i>';


    /**
     * @return array
     */
    public function getJavaScripts() {
        $assetsUrl = $this->modx->getOption('faselect.assets_url', null, MODX_ASSETS_URL . 'components/faselect/');
        return array(
            $assetsUrl . 'js/inputs/faselect.input.js',
        );
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        $tpls = array();

        // Grab the template from a .tpl file
        $corePath = $this->modx->getOption('faselect.core_path', null, MODX_CORE_PATH . 'components/faselect/');

        $template = file_get_contents($corePath . 'templates/faselect.tpl');

        // Wrap the template, giving the input a reference of "faselect", and
        // add it to the returned array.
        $tpls[] = $this->contentBlocks->wrapInputTpl('faselect', $template);
        return $tpls;
    }

    public function getName()
    {
        return 'Font Awesome Select';
        // return $this->modx->lexicon('faselect.input_name');
    }

    public function getDescription()
    {
        return 'Select box (available placeholders: [[+value]], [[+display]]';
        // return $this->modx->lexicon('faselect.input_description');
    }

    public function getFieldProperties()
    {
        return array(
            array(
                'key' => 'default_value',
                'fieldLabel' => 'Default Value',
                'xtype' => 'textfield',
                'default' => '',
                'description' => 'Default value to use, leave blank if none'
            ),
        );
    }
}
