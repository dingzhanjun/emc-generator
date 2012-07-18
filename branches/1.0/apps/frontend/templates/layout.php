<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
  <?php
  $sf_context->getUser()->setCulture('vi');
  ?>
  <div id='bodylight'>
  	<table width='100%' cellpadding="0" cellspacing="0">
    	<tr>
        	<td width = '136'></td>
            <td width = ''>
            	<div class='main_content'>
                	<div style='padding:10px 20px; position:relative;'>
                        <div id='logo'><img src='/images/logo.png' /></div>
						<?php
							if ($sf_context->getModuleName() != 'default' && $sf_context->getUser()->isAuthenticated()) {
								echo get_partial('default/tabs');
							}
						?>
                    </div>
                    <div class='bg_main_content'>
                    	<div style='padding:0 20px;'>
							<?php echo $sf_content ?>
                            <div style='display:block;width:100%;height:116px'>
                            <?php
                                if ($sf_context->getModuleName() != 'default' && $sf_context->getUser()->isAuthenticated()) {
									echo get_partial('default/signout');
                                }
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td width = '136'></td>
        </tr>
    </table>
  </div>	
  </body>
</html>