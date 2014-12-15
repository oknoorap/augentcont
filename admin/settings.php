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
		<p>Insert keywords separated by line.</p>
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
				<div id="icons" data-ng-show="$root.expandIcons">
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
		<form method="POST" enctype="multipart/form-data" action="index.php">
		<p>Quick setting as <a data-ng-click="setConfig('pdf')">PDF Folder</a> or <a data-ng-click="setConfig('html')">PDF search engine</a></p>
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
			<?php echo ($name === 'method') ? '<script type="text/javascript">var engineType = \''. $value .'\';</script>': ''; ?>
			<div class="row" <?php echo ($name === 'bing.api') ? 'data-ng-show="engineType === \'api\'"': ''; ?>>
				<div class="small-2 columns">
					<label for="<?php echo $id; ?>" class="right inline"><?php echo $label; ?></label>
				</div>
				<div class="small-7 end columns">
					<?php if ($name === 'logo'):
					if ($value !== ''):
					?>
					<img src="../content/logo/<?php echo $value; ?>" style="max-width: 300px; height: auto" />
					<br />
					<?php endif; ?>
					<input type="file" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" style="width: 100px">
					<input type="hidden" name="config[logo_tmp]" value="<?php echo $value; ?>">

					<?php elseif ($name === 'password'): ?>
					<input type="password" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" placeholder="Password" value="<?php echo $value; ?>" style="width: 200px;display: inline-block;margin-right: 10px;"> <label style="display: inline-block"><input type="checkbox" data-ng-click="togglePassword($event)"> Show Password</label>

					<?php elseif (strpos($name, 'database') !== FALSE):
						$db_id = str_replace('database.', '', $name);
					?>
					<script>databases.<?php echo $db_id; ?> = '<?php echo $value; ?>';</script>
					<input type="<?php echo (strpos($name, 'password') !== false)? 'password': 'text'; ?>" id="<?php echo $id; ?>" name="config[<?php echo $name; ?>]" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>" style="width: 200px;display: inline-block;margin-right: 10px;" data-ng-model="databases.<?php echo $db_id; ?>"> <?php if (strpos($name, 'password') !== false): ?><label style="display: inline-block"><input type="checkbox" data-ng-click="togglePassword($event)"> Show Password</label><?php endif; ?>

					<?php elseif ($name === 'theme'): $themes = directory_map('../content/themes/'); ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px">
						<?php foreach($themes as $theme_name => $theme):
                        if($theme_name !== 'smartoptimizer'):
						if(is_string($theme_name)): ?>
						<option value="<?php echo $theme_name; ?>" <?php echo ($value === $theme_name)? 'selected="selected"':''; ?>><?php echo $theme_name; ?></option>
						<?php
						endif;
						endif;
						endforeach; ?>
					</select>

					<?php elseif ($name === 'capitalize'): ?>
					<select name="config[<?php echo $name; ?>]" id="<?php echo $id; ?>" style="width: 200px">
						<option value="true" <?php echo ($value)? 'selected="selected"':''; ?>>Yes</option>
						<option value="false" <?php echo (! $value)? 'selected="selected"':''; ?>>No</option>
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
				<button class="small button"><i class="fa fa-save"></i> Save Changes</button>
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