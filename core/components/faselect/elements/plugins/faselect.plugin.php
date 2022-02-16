<?php
/**
 * FASelect for ContentBlocks
 * @author YJ Tso @sepiariver
 * GPL, no warranties, etc.
 * @var modX $modx
 * @var ContentBlocks $contentBlocks
 * @var array $scriptProperties
 */
if ($modx->event->name == 'ContentBlocks_RegisterInputs') {
    // Load your own class. No need to require cbBaseInput, that's already loaded.
    $path = $modx->getOption('faselect.core_path', null, MODX_CORE_PATH . 'components/faselect/');
    require_once($path . 'elements/inputs/faselect.class.php');

    // Create an instance of your input type, passing the $contentBlocks var
    $instance = new FASelect($contentBlocks);

    // Pass back your input reference as key, and the instance as value
    $modx->event->output(array(
        'faselect' => $instance
    ));
}

if ($modx->event->name === 'OnManagerPageInit') {

    // check output file
    $validFile = false;
    $outputPath = $modx->getOption('faselect.output_path', null, $modx->getOption('assets_path') . 'components/faselect/js/');
    $outputFilename = $modx->getOption('faselect.output_filename', null, 'faselectinputoptions.json');
    $outputFileContent = file_get_contents($outputPath . $outputFilename);
    if ($outputFileContent) {
        $array = $modx->fromJSON($outputFileContent);
        if (is_array($array)) {
            $validFile = true;
        }
    }

    // check cache
    $cacheKey = $modx->getOption('cacheKey', $scriptProperties, 'fontawesomecsssource');
    $refreshOnCacheClear = $modx->getOption('refreshOnCacheClear', $scriptProperties, true);
    $provider = $modx->cacheManager->getCacheProvider('default');
    $css = $provider->get($cacheKey);
    if ($refreshOnCacheClear && !$css) $validFile = false;

    // if there's a valid file there's no more code to execute. Otherwise:
    if (!$validFile) {

        if (!file_exists($outputPath) || !is_dir($outputPath)) {

            $dir = mkdir($outputPath, 0755, true);
            if (!$dir || !is_writable($outputPath)) {

                $modx->log(modX::LOG_LEVEL_ERROR, '[FASelect] could not create the required json file! Check filesystem permissions.');
                return '';

            }

        }
        // source file
        $cssUrl = $modx->getOption('faselect.cssUrl', null, 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
        
        $uniqid = uniqid();
        $chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $cssUrlParsed = $chunk->process(array(), $cssUrl);

        // scan options
        $regexPrefix = $modx->getOption('regexPrefix', $scriptProperties, 'fa-');
        // scan options
        $regexSuffix = $modx->getOption('regexSuffix', $scriptProperties, ':before');
        // label text output options
        $titleCaseLabels = $modx->getOption('titleCaseLabels', $scriptProperties, 1);
        // value text output options
        $outputPrefix = $modx->getOption('classPrefix', $scriptProperties, 'fa-');
        // list output options
        $excludeClasses = array_filter(array_map('trim', explode(',', $modx->getOption('excludeClasses', $scriptProperties, 'ul,li'))));

        if (!$css) {
            
            // get source file
            if (preg_match('#^https?://#i', $cssUrlParsed) === 1) {
                $css = file_get_contents($cssUrlParsed, false);
            } else {
                
                $file = $modx->getOption('base_path') . $cssUrlParsed;
                
                if (!file_exists($file)) {
                  $modx->log(modX::LOG_LEVEL_ERROR, '[FASelect] provided input is not a file: ' . $file);
                  return;
                }
                
                $css = file_get_contents($file);
            }
            
            if ($css) {
                $provider->set($cacheKey, $css, 0);
            } else {
                $modx->log(modX::LOG_LEVEL_ERROR, '[FASelect] could not get css source!');
                return '';
            }
        }

        // output
        $output = array();
        $regex = "/\." . $regexPrefix . "([\w-]*)" . $regexSuffix . "/";
        if (preg_match_all($regex, $css, $matches)) {

            $icons = array_diff($matches[1], $excludeClasses);
            foreach($icons as $icon) {

                $label = ($titleCaseLabels) ? ucwords(str_replace('-', ' ', $icon)) : $icon;
                $output[$label] = $outputPrefix . $icon;

            }

        }

        if (!file_put_contents($outputPath . $outputFilename, $modx->toJSON($output))) {

            $modx->log(modX::LOG_LEVEL_ERROR, '[FASelect] could not write the required json file!');

        }

    }

}
