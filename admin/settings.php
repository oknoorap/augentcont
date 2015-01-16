<dl class="tabs">
	<?php if (config('installed')): ?><dd class="active"><a class="nav-panel" rel="panel-1"><i class="fa fa-file-text"></i> Insert Keywords</a></dd><?php endif; ?>
	<dd <?php echo (! config('installed'))? 'class="active"': ''; ?>><a class="nav-panel" rel="panel-2"><i class="fa fa-gear"></i> Config</a></dd>
	<dd data-ng-show="engineType === 'proxy'"><a class="nav-panel" rel="panel-3"><i class="fa fa-globe"></i> Check Proxy</a></dd>
	<?php if (!config('installed')): ?><dd><a class="nav-panel" rel="panel-4"><i class="fa fa-database"></i> Install</a></dd><?php endif; ?>
</dl>
<div class="tabs-content">
	<?php if (config('installed')):
	$db = new DB_Driver('localhost', config('database.name'), config('database.username'), config('database.password'));
	?>
	<div class="content active" id="panel-1">
		<p>Insert keywords separated by line. Total keywords <code><?php echo get_keyword_count(); ?></code>.</p>
		<form method="POST" enctype="multipart/form-data">
			<div id="insert-keyword-box" data-ng-if="! $root.onPrepare">
				<textarea data-ng-model="$root.keywords" style="height: 250px"></textarea>
				<div class="row collapse">
					<div class="small-1 columns">
						<label for="category" class="left inline">Category</label>
					</div>
					<?php
						$categories = $db->get('cat', 10000)->result();
						if (!isset($categories[0])) $categories = array($categories);
					?>
					<div class="small-3 end columns">
						<select id="category" name="category" data-ng-model="$root.selectedCategory" data-ng-change="changeCategory()">
							<?php foreach ($categories as $index => $category): ?>
							<option data-icon="<?php echo $category['icon']; ?>" value="<?php echo $category['name']; ?>"><?php echo ucwords($category['name']); ?></option>
							<?php endforeach; ?>
							<option data-icon="ellipsis-h" value="new">New category</option>
						</select>
					</div>
					<div class="small-1 end columns">
						<a class="icon small-12 columns tiny button" data-ng-click="toggleExpandIcon()"><i class="fa fa-{{ $root.selectedIcon }}"></i></a>
					</div>
					<div class="small-7 end columns" id="new-category-box" data-ng-if="$root.selectedCategory === 'new'">
						<input type="text" data-ng-model="$root.newCategory" placeholder="e.g Business">
					</div>
				</div>
				<div id="icons" data-ng-show="$root.expandIcons" style="display:none">
					<a class="tiny secondary button" data-ng-repeat="i in icon track by $index" data-ng-class="{'success': $root.selectedIcon === i}" ng-click="$root.selectedIcon = i"><i class="fa fa-{{ i }}"></i></a>
				</div>
			</div>
			<div id="table" data-ng-if="$root.onPrepare"></div>

			<p data-ng-if="$root.onPrepare"><button class="tiny success button" data-ng-click="unPrepare($event)" data-ng-class="{disabled: $root.onProgress}"><i class="fa fa-chevron-left"></i> Previous Step</button> <button class="tiny button" data-ng-click="insertKeyword($event)" data-ng-class="{disabled: noRecords() || $root.onProgress}"><i class="fa fa-plus-square"></i> Insert Keyword</button></p>
			<p data-ng-if="! $root.onPrepare"><button class="tiny success button" data-ng-click="prepareKeywords()">Next Step <i class="fa fa-chevron-right"></i></button></p>
		</form>
	</div>
	<?php endif; ?>

	<div class="content <?php echo (! config('installed'))? 'active': ''; ?>" id="panel-2">
		<form id="config" method="POST" enctype="multipart/form-data" action="index.php">
		
		<div class="callout panel">
			<p><strong>Quick setting as</strong> <a class="tiny button" data-ng-click="setConfig('pdf')"><i class="fa fa-folder"></i> PDF Directory</a> <a class="tiny button" data-ng-click="setConfig('html')"><i class="fa fa-file-pdf-o"></i> PDF Search Engine</a> <a class="tiny button" onclick="alert('Coming Soon!');return false;"><i class="fa fa-youtube-play"></i> YouTube Engine</a></p>
		</div>

		<script>var databases = {};</script>
		<?php foreach (config() as $name => $value):
			$id = str_replace('.', '-', $name);
			$label = str_replace('.', ' ', normalize($name));
			$label = ucwords($label);
			$value = htmlentities($value);
			?>
			
			<?php if ($name === 'installed'): ?>
			<input type="hidden" name="config[<?php echo $name; ?>]" value="<?php echo $value; ?>">
			<?php else: ?>
			
			<?php if ($name === 'method' || $name === 'using.spinner'): ?>
			<script type="text/javascript"><?php if ($name === 'method'): ?>var engineType = '<?php echo $value; ?>'; <?php elseif ($name === 'using.spinner'): ?>var usingSpinner = '<?php echo ($value) ? 'true': 'false'; ?>'; <?php endif; ?> </script>
			<?php endif; ?>

			<?php
			if ($name === 'database.username'):
				echo "<hr data-content=\"Database\" />";
			elseif ($name === 'theme'):
				echo "<hr data-content=\"Theme &amp; SEO\" />";
			elseif ($name === 'capitalize'):
				echo "<hr data-content=\"Permalinks\" />";
			elseif ($name === 'boost.mode'):
				echo "<hr data-content=\"Content Settings\" />";
			elseif ($name === 'search.query'):
				echo "<hr data-content=\"Engine\" />";
			elseif ($name === 'header.script'):
				echo "<hr data-content=\"Scripts &amp; Logo\" />";
			endif;
			?>

			<div class="row" <?php echo ($name === 'bing.api') ? 'data-ng-show="engineType === \'api\'"': ''; ?>>
				<div class="small-2 columns">
					<label for="<?php echo $id; ?>" class="right inline"><?php echo $label; ?></label>
				</div>
				<div class="small-7 end columns">
					<?php
					if ($name === 'logo'): ?>
					<canvas id="canvas" width="0" height="0" data-url="<?php echo (! empty($value)) ? '../content/logo/'. $value:'' ?>">Browser missing HTML5 support.</canvas> <br />
					<input type="file" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" />
					<input type="hidden" name="config[logo_tmp]" id="logo_tmp" value="no">
					<input type="hidden" name="config[logo_old]" value="<?php echo $value; ?>">

					<?php elseif ($name === 'password'): ?>
					<input type="password" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" placeholder="Password" value="<?php echo $value; ?>" style="width: 200px;display: inline-block;margin-right: 10px;"> <label style="display: inline-block"><input type="checkbox" data-ng-click="togglePassword($event)"> Show Password</label>

					<?php elseif (strpos($name, 'database') !== FALSE):
						$db_id = str_replace('database.', '', $name);
					?>
					<script>databases.<?php echo $db_id; ?> = '<?php echo $value; ?>';</script>
					<input type="<?php echo (strpos($name, 'password') !== false)? 'password': 'text'; ?>" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>" style="width: 200px;display: inline-block;margin-right: 10px;" data-ng-model="databases.<?php echo $db_id; ?>"> <?php if (strpos($name, 'password') !== false): ?><label style="display: inline-block"><input type="checkbox" data-ng-click="togglePassword($event)"> Show Password</label><?php endif; ?>

					<?php elseif ($name === 'theme'): $themes = directory_map('../content/themes/'); ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px">
						<?php
						$theme_prefix = array('dir-', 'pdf-', 'video-');
						$theme_dir = array();
						$theme_pdf = array();
						/*$theme_video = array();*/

						foreach($themes as $theme => $files)
						{
							if($theme !== 'smartoptimizer' && is_string($theme))
							{
								$theme_name = str_replace($theme_prefix, '', $theme);
								$theme_name = normalize($theme_name, TRUE);

								if (strpos($theme, 'dir-') !== FALSE)
								{
									array_push($theme_dir, array('name' => str_replace('Pdf', 'PDF', $theme_name), 'dir' => $theme));
								}
								elseif (strpos($theme, 'pdf-') !== FALSE)
								{
									array_push($theme_pdf, array('name' => $theme_name, 'dir' => $theme));
								}
							}
						}

						if (! empty($theme_dir))
						{
							echo '<optgroup label="Directory">';
							foreach ($theme_dir as $theme)
							{
								?><option value="<?php echo $theme['dir']; ?>" <?php echo ($value === $theme['dir'])? 'selected="selected"':''; ?>><?php echo $theme['name']; ?></option><?php
							}
							echo '</optgroup>';
						}

						if (! empty($theme_pdf))
						{
							echo '<optgroup label="PDF Search Engine">';
							foreach ($theme_pdf as $theme)
							{
								?><option value="<?php echo $theme['dir']; ?>" <?php echo ($value === $theme['dir'])? 'selected="selected"':''; ?>><?php echo $theme['name']; ?></option><?php
							}
							echo '</optgroup>';
						}
						?>
					</select>

					<?php elseif ($name === 'capitalize' || $name === 'boost.mode'|| $name === 'using.spinner'): ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px" <?php echo ($name ==='using.spinner')? ' data-ng-model="usingSpinner"': ''; ?>>
						<option value="true" <?php echo ($value)? 'selected="selected"':''; ?>>Yes</option>
						<option value="false" <?php echo (! $value)? 'selected="selected"':''; ?>>No</option>
					</select>

					<?php elseif ($name === 'spinner.method'): ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px">
						<option value="static" <?php echo ($value === 'static')? 'selected="selected"': ''; ?>>Static</option>
						<option value="dynamic" <?php echo ($value === 'dynamic')? 'selected="selected"': ''; ?>>Dynamic</option>
					</select>

					<?php elseif ($name === 'separator'): ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px">
						<option value="-" <?php echo ($value === '-')? 'selected="selected"': ''; ?>>-</option>
						<option value="_" <?php echo ($value === '_')? 'selected="selected"': ''; ?>>_</option>
					</select>

					<?php elseif ($name === 'type'): ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px">
						<option value="pdf" <?php echo ($value === 'pdf')? 'selected="selected"': ''; ?>>PDF</option>
						<option value="html" <?php echo ($value === 'html')? 'selected="selected"': ''; ?>>HTML</option>
					</select>

					<?php elseif ($name === 'method'): ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px" data-ng-model="engineType">
						<option value="api" <?php echo ($value === 'api')? 'selected="selected"': ''; ?>>API</option>
						<option value="proxy" <?php echo ($value === 'proxy')? 'selected="selected"': ''; ?>>Proxy</option>
					</select>

					<?php elseif ($name === 'bing.api'): ?>
					<textarea style="height: 200px" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" data-ng-show="engineType === 'api'"><?php echo $value; ?></textarea>

					<?php elseif ($name === 'bad.words' || $name === 'clean.words'|| $name === 'header.script'|| $name === 'footer.script'): ?>
					<textarea style="height: 200px" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]"><?php echo $value; ?></textarea>

					<?php elseif ($name === 'single_var' || $name === 'results'): ?>
					<input type="text" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>" style="width: 100px">

					<?php else: ?>
					<input type="text" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>">

					<?php endif; ?>

				</div>
			</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<input type="hidden" name="type" value="config">
		<div class="row">
			<div class="small-2 columns">&nbsp;</div>
			<div class="small-10 columns">
				<a class="small button save-config"><i class="fa fa-save"></i> <span class="txt">Save Changes</span></a>
			</div>
		</div>
		</form>
	</div>

	<div class="content" id="panel-3">
		<a data-ng-click="proxyTest()" class="tiny button"><i class="fa fa-exchange"></i> Test Proxy</a>
		<pre id="log"></pre>
	</div>

	<?php if(!config('installed')): ?>
	<div class="content" id="panel-4">
		<p>
		<strong>Database Username:</strong> <span ng-bind="databases.username"></span><br />
		<strong>Database Password:</strong> <span ng-bind="databases.password"></span><br />
		<strong>Database Name:</strong> <span ng-bind="databases.name"></span>
		</p>

		<p>
			<span style="font-size: 18px;">Status <span style="color: #008000" data-ng-show="databases.status">OK</span><span style="color: #cc0000" data-ng-show="! databases.status">UNCHECKED / FAILED</span></span>
		</p>

		<a class="button" data-ng-click="checkDatabase()" data-ng-class="{disabled: checkDbProgress}" data-ng-show="! databases.status"><i class="fa fa-database"></i> Check DB</a>
		<a class="success button" data-ng-click="installDatabase()" data-ng-class="{disabled: installDbProgress}" data-ng-show="databases.status"><i class="fa fa-database"></i> Install DB</a>
	</div>
	<?php endif; ?>
</div>