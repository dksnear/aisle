<?php
namespace aisle\proj\shell;

use aisle\core\File;
use aisle\web\Controller;

class SyncController extends Controller{
	
	
	public function Aisle(){
		
		// print_r(File::Sync(

			// array(
				
				// 'G:\a-projs\_local-servers\server-2',
				// 'G:\a-projs\aisle2',
				// 'G:\win8\SkyDrive\$bak\_local-servers\server-2'
			// )

		// ,'/\.git|\.svn/',32 | 64));
		
		
		print_r(File::Sync(

			array(
				
				'G:\a-projs\_local-servers\server-2',
				'G:\a-projs\aisle2',
				'G:\win8\SkyDrive\$bak\_local-servers\server-2'
			)

		,'/\.git|\.svn/',4 | 64));
		
		return 1;
	}
	

}