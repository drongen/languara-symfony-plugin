<?php

namespace Languara\SymfonyBundle\Library;

include_once 'lib_languara.php';

class LanguaraWrapper extends \Lib_Languara
{
    public function __construct($docroot)
    {
        include_once __DIR__ .'/../Resources/config/config.php';
        
        $this->conf                 = $config['conf'];
        $this->language_location    = $docroot . $config['language_location'] .'/';
        $this->endpoints            = $config['endpoints'];
    }
    
    protected function retrive_local_translations()
	{
		// get local locales, resource groups, and translations
		$dir_iterator = new \DirectoryIterator($this->language_location);		
		$arr_locales = array();
		
		foreach ($dir_iterator as $file)
		{	
			// skip the system files for navigation and language_backup directory
			if($file->getFilename() != '.' && $file->getFilename() != '..' && $file->getFilename() != 'language_backup')
			{
				if (! $file->isDir())
				{                                     
                    $lang = include ($file->getRealPath());

                    if (! isset($lang)) continue;
                    
                    // the name of the files in symfony is resource_group.locale_name_eng,
                    // we need to explode the name and get the parts
                    
                    $arr_filename_parts     = explode('.', $file->getFilename());
					$resource_group_name    = $arr_filename_parts[0];
                    $locale                 = $arr_filename_parts[1];
                    
					$arr_locales[$locale][$resource_group_name] = $lang;
				}
			}
		}
        
        return $arr_locales;
	}
    
    protected function add_translations_to_files()
    {
        // process locale
		foreach ($this->arr_project_locales as $project_locale) 
		{			
			// process translations
			foreach ($this->arr_resource_groups as $resource_group)
			{
                
				$resource_group_file_contents = $this->get_file_header();
				foreach ($this->arr_translations as $translation) 
				{					
					if ($translation->resource_group_id == $resource_group->resource_group_id && $project_locale->locale_id == $translation->locale_id)
					{						
						$resource_group_file_contents .= $this->get_file_content($translation->resource_cd, $translation->translation_txt);
					}
				}
                
                $resource_group_file_contents .= $this->get_file_footer();
                
                $file_path = $this->language_location . strtolower($this->conf['file_prefix'] . $resource_group->resource_group_name .'.'. $project_locale->iso_639_1 . $this->conf['file_suffix'] .'.php');
                
				file_put_contents($file_path, $resource_group_file_contents);
                chmod($file_path, 0777);
			}
		}	
    }
}