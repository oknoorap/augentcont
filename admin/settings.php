<dl class="tabs">
	<?php if (config('installed')): ?><dd class="active"><a class="nav-panel" rel="panel-1"><i class="fa fa-file-text"></i> Insert Keywords</a></dd><?php endif; ?>
	<dd <?php echo (! config('installed'))? 'class="active"': ''; ?>><a class="nav-panel" rel="panel-2"><i class="fa fa-gear"></i> Config</a></dd>
	<dd data-ng-show="engineType === 'proxy'"><a class="nav-panel" rel="panel-3"><i class="fa fa-globe"></i> Check Proxy</a></dd>
	<dd><a class="nav-panel" rel="panel-4"><i class="fa fa-key"></i> FTP &amp; phpMyAdmin</a></dd>
	<?php if (!config('installed')): ?><dd><a class="nav-panel" rel="panel-5"><i class="fa fa-database"></i> Install</a></dd><?php endif; ?>
</dl>
<div class="tabs-content">
	<?php if (config('installed')):
	$db = new DB_Driver('localhost', config('database.name'), config('database.username'), config('database.password'));
	?>
	<div class="content active" id="panel-1">
		<p>Insert keywords separated by line. Total keywords <code><?php echo get_keyword_count(); ?></code>.<br />
		All: <a data-ng-click="genKeyword('')" title="All languages">random</a>,
		<a data-ng-click="genKeyword('en')" title="English">EN</a>,
		<a data-ng-click="genKeyword('da')" title="Danish">DK</a>,
		<a data-ng-click="genKeyword('de')" title="German">DE</a>,
		<a data-ng-click="genKeyword('ru')" title="Russia">RU</a>,
		<a data-ng-click="genKeyword('it')" title="Italy">IT</a>,
		<a data-ng-click="genKeyword('no')" title="Norwegia">NO</a>,
		<a data-ng-click="genKeyword('fr')" title="French">FR</a>,
		<a data-ng-click="genKeyword('se')" title="Sweden">SE</a>,
		<a data-ng-click="genKeyword('se')" title="Spanish">ES</a>,
		<a data-ng-click="genKeyword('fi')" title="Finland">FI</a>,
		<a data-ng-click="genKeyword('nl')" title="Netherlands">NL</a>,
		<a data-ng-click="genKeyword('ja')" title="Japan">JP</a>,
		<a data-ng-click="genKeyword('zh')" title="Chinese">ZH</a>,
		<a data-ng-click="genKeyword('id')" title="Indonesia">ID</a>,
		or manually.<br />
		Books: <a data-ng-click="genKeyword('', 'b')" title="All languages">random</a>,
		<a data-ng-click="genKeyword('en', 'b')" title="English">EN</a>,
		<a data-ng-click="genKeyword('da', 'b')" title="Danish">DK</a>,
		<a data-ng-click="genKeyword('de', 'b')" title="German">DE</a>,
		<a data-ng-click="genKeyword('ru', 'b')" title="Russia">RU</a>,
		<a data-ng-click="genKeyword('it', 'b')" title="Italy">IT</a>,
		<a data-ng-click="genKeyword('no', 'b')" title="Norwegia">NO</a>,
		<a data-ng-click="genKeyword('fr', 'b')" title="French">FR</a>,
		<a data-ng-click="genKeyword('se', 'b')" title="Sweden">SE</a>,
		<a data-ng-click="genKeyword('se', 'b')" title="Spanish">ES</a>,
		<a data-ng-click="genKeyword('fi', 'b')" title="Finland">FI</a>,
		<a data-ng-click="genKeyword('nl', 'b')" title="Netherlands">NL</a>,
		<a data-ng-click="genKeyword('ja', 'b')" title="Japan">JP</a>,
		<a data-ng-click="genKeyword('zh', 'b')" title="Chinese">ZH</a>,
		<a data-ng-click="genKeyword('id', 'b')" title="Indonesia">ID</a>,
		or manually.</p>
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
				<div id="icons" class="hide" data-ng-show="$root.expandIcons">
					<a class="tiny secondary button" data-ng-repeat="i in icon track by $index" data-ng-class="{'success': $root.selectedIcon === i}" ng-click="$root.selectedIcon = i"><i class="fa fa-{{ i }}"></i></a>
				</div>
			</div>
			<div id="table" data-ng-if="$root.onPrepare"></div>

			<p data-ng-if="$root.onPrepare"><button class="tiny success button" data-ng-click="unPrepare($event)" data-ng-class="{disabled: $root.onProgress}"><i class="fa fa-chevron-left"></i> Previous Step</button> <button class="tiny button" data-ng-click="insertKeyword($event)" data-ng-class="{disabled: noRecords() || $root.onProgress}"><i class="fa fa-plus-square"></i> Insert <span data-ng-bind="keywordsCount()"></span> Keyword</button></p>
			<p data-ng-if="! $root.onPrepare"><button class="tiny success button" data-ng-click="prepareKeywords()">Next Step <i class="fa fa-chevron-right"></i></button></p>
		</form>
	</div>
	<?php endif; ?>

	<div class="content hide <?php echo (! config('installed'))? 'active': ''; ?>" id="panel-2">
		<dl class="sub-nav">
			<dd data-ng-class="{active: settings==='general'}"><a data-ng-click="viewSettings('general')"><i class="fa fa-gear"></i> General</a></dd>
			<dd data-ng-class="{active: settings==='seoperma'}"><a data-ng-click="viewSettings('seoperma')"><i class="fa fa-chain"></i> Permalinks / SEO</a></dd>
			<dd data-ng-class="{active: settings==='themes'}"><a data-ng-click="viewSettings('themes')"><i class="fa fa-columns"></i> Themes</a></dd>
			<dd data-ng-class="{active: settings==='stopwords'}"><a data-ng-click="viewSettings('stopwords')"><i class="fa fa-strikethrough"></i> Stop Words</a></dd>
			<dd data-ng-class="{active: settings==='pdb'}"><a data-ng-click="viewSettings('pdb')"><i class="fa fa-database"></i> Password / DB</a></dd>
			<dd data-ng-class="{active: settings==='scripts'}"><a data-ng-click="viewSettings('scripts')" class="sub-nav"><i class="fa fa-code"></i> Scripts</a></dd>
			<dd data-ng-class="{active: settings==='monetize'}"><a data-ng-click="viewSettings('monetize')" class="sub-nav"><i class="fa fa-dollar"></i> Monetize</a></dd>
		</dl>
		<form id="config" method="POST" enctype="multipart/form-data" action="index.php">
			<div data-ng-show="settings === 'general'">
				<div class="row">
					<div class="small-2 columns">
						<label for="results" class="right inline">Quick settings as</label>
					</div>
					<div class="small-7 end columns">
						<p>
							<a data-ng-click="setConfig('pdf')" class="quick-settings tiny button"><i class="fa fa-file-pdf-o"></i> PDF Directory</a> or 
							<a data-ng-click="setConfig('html')" class="quick-settings tiny button"><i class="fa fa-search"></i> PDF Search Engine</a>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="small-2 columns">
						<label for="results" class="right inline">Results</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="results" name="config[results]" placeholder="Results" value="<?php echo config('results', true); ?>" style="width: 65px">
						<p class="description">Expected result count from bing.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="boost-mode" class="right inline">Boost Mode</label>
					</div>
					<div class="small-7 end columns">
						<div class="switch">
							<input id="boost-mode" type="checkbox" <?php echo (config('boost.mode', true)) ? 'checked="checked"': ''; ?> data-ng-model="boostMode">
							<label for="boost-mode"></label>
						</div>
						<input type="hidden" name="config[boost.mode]" data-ng-model="boostMode" value="{{ boostMode }}">
						<p class="description">Whether generates new content for each title in result page or disabled it (AGC).</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="using-spinner" class="right inline">Spinner</label>
					</div>
					<div class="small-7 end columns">
						<div class="switch">
							<input name="config[using.spinner]" id="using-spinner" type="checkbox" <?php echo (config('using.spinner', true)) ? 'checked="checked"': ''; ?> data-ng-model="usingSpinner">
							<label for="using-spinner"></label>
						</div>
						<input type="hidden" name="config[using.spinner]" data-ng-model="usingSpinner" value="{{ usingSpinner }}">
						<p class="description">Whether display content spinner in result page or not.</p>
					</div>
				</div>

				<?php $spinner_method = config('spinner.method', true); ?>
				<div class="row" data-ng-show="usingSpinner">
					<div class="small-2 columns">
						<label for="spinner-method" class="right inline">Spinner Type</label>
					</div>
					<div class="small-7 end columns">
						<select name="config[spinner.method]" id="spinner-method" style="width: 200px">
							<option value="static" <?php echo ($spinner_method === 'static')? 'selected="selected"': ''; ?>>Static</option>
							<option value="dynamic" <?php echo ($spinner_method === 'dynamic')? 'selected="selected"': ''; ?>>Dynamic</option>
						</select>
						<p class="description">Whether display static spinner or dynamically.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="search-query" class="right inline">Search Query</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="search-query" name="config[search.query]" placeholder="Search Query" value="<?php echo config('search.query', true); ?>">
						<p class="description">Bing Search Query, <a target="_blank" href="https://msdn.microsoft.com/en-us/library/ff795620.aspx">read more</a>.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="method" class="right inline">Method</label>
					</div>
					<div class="small-7 end columns">
						<select name="config[method]" id="method" style="width: 200px" data-ng-model="engineType">
							<option value="api" >API</option>
							<option value="proxy" selected="selected">Proxy</option>
						</select>
						<p class="description">Whether use proxy when searching on bing or using API key.</p>
					</div>
				</div>

				<div class="row" data-ng-show="engineType === 'api'">
					<div class="small-2 columns">
						<label for="bing-api" class="right inline">Bing Api</label>
					</div>
					<div class="small-7 end columns">
						<textarea style="height: 200px" id="bing-api" name="config[bing.api]" data-ng-show="engineType === 'api'"><?php echo config('bing.api', true); ?></textarea>
					</div>
				</div>

				<?php $logo = config('logo', true); ?>
				<div class="row">
					<div class="small-2 columns">
						<label for="logo" class="right inline">Logo</label>
					</div>
					<div class="small-7 end columns">
						<canvas id="canvas" width="0" height="0" data-url="<?php echo (! empty($logo)) ? '../content/logo/'. $logo:'' ?>">Browser missing HTML5 support.</canvas> <br />
						<input type="file" id="logo" name="config[logo]" />
						<input type="hidden" name="config[logo_tmp]" id="logo_tmp" value="no">
						<input type="hidden" name="config[logo_old]" value="<?php echo $logo; ?>">
					</div>
				</div>
			</div>

			<?php $themes = directory_map('../content/themes/'); ?>
			<div data-ng-show="settings==='themes'">
				<?php
				$theme_prefix = array('dir-', 'pdf-', 'video-');
				$theme_dir = array();
				$theme_pdf = array();
				/*$theme_video = array();*/

				foreach($themes as $theme => $files)
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
				?>

				<h3>Search Engine</h3>
				<?php foreach (array_chunk($theme_pdf, 4) as $theme): ?>
					<div class="row">
						<?php foreach ($theme as $thm):
							$thumb = pathjoin('../content', 'themes', $thm['dir'], 'thumbnail.png');
							$bg = (file_exists($thumb))? $thumb: base_url() . 'assets/img/nothumb.jpg';
						?>
							<div class="small-3 end columns">
								<div class="theme-selector" data-ng-click="selectTheme('<?php echo $thm['dir']; ?>')" data-ng-class="{selected: theme === '<?php echo $thm['dir']; ?>'}">
									<img src="<?php echo $bg; ?>" width="220" height="220" alt="<?php echo $thm['name']; ?>">
									<div class="description"><?php echo $thm['name']; ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>

				<p><br /></p>
				<h3>Directory</h3>
				<?php foreach (array_chunk($theme_dir, 4) as $theme): ?>
					<div class="row">
						<?php foreach ($theme as $thm):
							$thumb = pathjoin('../content', 'themes', $thm['dir'], 'thumbnail.png');
							$bg = (file_exists($thumb))? $thumb: base_url() . 'assets/img/nothumb.jpg';
						?>
							<div class="small-3 end columns">
								<div class="theme-selector" data-ng-click="selectTheme('<?php echo $thm['dir']; ?>')" data-ng-class="{selected: theme === '<?php echo $thm['dir']; ?>'}">
									<img src="<?php echo $bg; ?>" width="220" height="220" alt="<?php echo $thm['name']; ?>">
									<div class="description"><?php echo $thm['name']; ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				<input type="hidden" name="config[theme]" data-ng-model="theme" value="{{ theme }}">
			</div>

			<div data-ng-show="settings==='seoperma'">
				<div class="row">
					<div class="small-2 columns">
						<label for="capitalize" class="right inline">Capitalize</label>
					</div>
					<div class="small-7 end columns">
						<div class="switch">
							<input name="config[capitalize]" id="capitalize" type="checkbox" <?php echo (config('capitalize', true)) ? 'checked="checked"': ''; ?> data-ng-model="permalinks.capitalize">
							<label for="capitalize"></label>
						</div>
						<input type="hidden" name="config[capitalize]" data-ng-model="permalinks.capitalize" value="{{ permalinks.capitalize }}">
					</div>
				</div>

				<?php $separator = config('separator', true); ?>
				<div class="row">
					<div class="small-2 columns">
						<label for="separator" class="right inline">Separator</label>
					</div>
					<div class="small-7 end columns">
						<select name="config[separator]" id="separator" style="width: 200px" data-ng-model="permalinks.separator">
							<option value="-" <?php echo ($separator === '-')? 'selected="selected"': ''; ?>>-</option>
							<option value="_" <?php echo ($separator === '_')? 'selected="selected"': ''; ?>>_</option>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="single_var" class="right inline">Single Var</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="single_var" name="config[single_var]" placeholder="Single Variable" value="<?php echo config('single_var', true); ?>" style="width: 100px" data-ng-model="permalinks.single_var">
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">&nbsp;</div>
					<div class="small-10 columns">
						<div class="panel callout">
							<p><strong>Permalink</strong><br /><code><?php echo base_url(); ?><span data-ng-bind="buildPermalink('category page')"></span>/<span data-ng-bind="buildPermalink('result page')"></span>.html?<span data-ng-bind="permalinks.single_var"></span>=ID</code></p>
						</div>
					</div>
				</div>

				<p><br /></p>

				<div class="row">
					<div class="small-2 columns">
						<label for="index-title" class="right inline">Index Title</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="index-title" name="config[index.title]" placeholder="Index Title" value="<?php echo config('index.title', true); ?>">
						<p class="description">Homepage's title.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="index-description" class="right inline">Index Description</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="index-description" name="config[index.description]" placeholder="Index Description" value="<?php echo config('index.description', true); ?>">
						<p class="description">Homepage's meta description.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="category-title" class="right inline">Category Title</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="category-title" name="config[category.title]" placeholder="Category Title" value="<?php echo config('category.title', true); ?>">
						<p class="description">Category page title</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="category-description" class="right inline">Category Description</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="category-description" name="config[category.description]" placeholder="Category Description" value="<?php echo config('category.description', true); ?>">
						<p class="description">Category meta description.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="result-title" class="right inline">Result Title</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="result-title" name="config[result.title]" placeholder="Result Title" value="<?php echo config('result.title', true); ?>">
						<p class="description">Result's title.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="result-description" class="right inline">Result Description</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="result-description" name="config[result.description]" placeholder="Result Description" value="<?php echo config('result.description', true); ?>">
						<p class="description">Result's meta description.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="single-title" class="right inline">Single Title</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="single-title" name="config[single.title]" placeholder="Single Title" value="<?php echo config('single.title', true); ?>">
						<p class="description">Single page's title.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="single-description" class="right inline">Single Description</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="single-description" name="config[single.description]" placeholder="Single Description" value="<?php echo config('single.description', true); ?>">
						<p class="description">Single page's meta description.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label class="right inline">Available syntax</label>
					</div>
					<div class="small-7 end columns">
						<p>
							<ul class="square">
								<li><code>{category}</code> - current category.</li>
								<li><code>{keyword}</code> - current keyword (only support in result's page).</li>
								<li><code>{domain}</code> - your domain, e.g <code>mywebsite.com</code>.</li>
								<li><code>{site_url}</code> - your website's url, e.g <code>http://mywebsite.com</code>.</li>
								<li><code>{site_name}</code> - your website's hostname, e.g <code>mywebsite</code>.</li>
							</ul>
						</p>
					</div>
				</div>
			</div>

			<div data-ng-show="settings==='stopwords'">
				<div class="row">
					<div class="small-2 columns">
						<label for="clean-words" class="right inline">Clean Words</label>
					</div>
					<div class="small-7 end columns">
						<textarea style="height: 200px" id="clean-words" name="config[clean.words]"><?php echo config('clean.words', true); ?></textarea>
						<p class="description">Clean title or description from these words.</p>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="bad-words" class="right inline">Bad Words</label>
					</div>
					<div class="small-7 end columns">
						<textarea style="height: 200px" id="bad-words" name="config[bad.words]"><?php echo config('bad.words', true); ?></textarea>
						<p class="description">Remove result when title or description contains these words.</p>
					</div>
				</div>
			</div>

			<div data-ng-show="settings==='pdb'">
				<div class="row">
					<div class="small-2 columns">
						<label for="password" class="right inline">Password</label>
					</div>
					<div class="small-7 end columns">
						<input type="password" id="password" name="config[password]" placeholder="Password" value="<?php echo config('password', true); ?>" style="width: 200px;display: inline-block;margin-right: 10px;"> <label style="display: inline-block"><input type="checkbox" data-ng-click="togglePassword($event)"> Show Password</label>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="database-username" class="right inline">Database Username</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="database-username" name="config[database.username]" placeholder="Database Username" value="<?php echo config('database.username', true); ?>" style="width: 200px;display: inline-block;margin-right: 10px;" data-ng-model="databases.username"> 
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="database-password" class="right inline">Database Password</label>
					</div>
					<div class="small-7 end columns">
						<input type="password" id="database-password" name="config[database.password]" placeholder="Database Password" value="<?php echo config('database.password', true); ?>" style="width: 200px;display: inline-block;margin-right: 10px;" data-ng-model="databases.password"> <label style="display: inline-block"><input type="checkbox" data-ng-click="togglePassword($event)"> Show Password</label>
					</div>
				</div>

				<div class="row">
					<div class="small-2 columns">
						<label for="database-name" class="right inline">Database Name</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="database-name" name="config[database.name]" placeholder="Database Name" value="<?php echo config('database.name', true); ?>" style="width: 200px;display: inline-block;margin-right: 10px;" data-ng-model="databases.name"> 

					</div>
				</div>
			</div>

			<div data-ng-show="settings==='scripts'">
				<div class="row" >
					<div class="small-2 columns">
						<label for="header-script" class="right inline">Header Script</label>
					</div>
					<div class="small-7 end columns">
						<textarea style="height: 200px" id="header-script" name="config[header.script]"><?php echo config('header.script', true); ?></textarea>
						<p class="description">html/javascript/css inside <code>&lt;head&gt;</code> tag.</p>
					</div>
				</div>

				<div class="row" >
					<div class="small-2 columns">
						<label for="footer-script" class="right inline">Footer Script</label>
					</div>
					<div class="small-7 end columns">
						<textarea style="height: 200px" id="footer-script" name="config[footer.script]"><?php echo config('footer.script', true); ?></textarea>
						<p class="description">html/javascript before <code>&lt;/body&gt;</code> tag.</p>
					</div>
				</div>
			</div>

			<div data-ng-show="settings==='monetize'">
				<div class="row">
					<div class="small-2 columns">
						<label for="cpa_url" class="right inline">CPA URL</label>
					</div>
					<div class="small-7 end columns">
						<input type="text" id="cpa_url" name="config[cpa_url]" placeholder="Cpa Url" value="<?php echo config('cpa_url', true); ?>">
						<p class="description"><code>{q}</code> will be replaced with current keyword.</p>
					</div>
				</div>
			</div>

			<input type="hidden" name="config[installed]" value="1">
			<input type="hidden" name="config[type]" value="<?php echo config('type', true); ?>">
			<input type="hidden" name="type" value="config">
			<script>
				var databases = {username: '<?php echo config("database.username", true); ?>', password: '<?php echo config("database.password", true); ?>', name: '<?php echo config("database.name", true); ?>'},
				engineType = '<?php echo config("method", true); ?>',
				boostMode = <?php echo (config("boost.mode", true))? "true": "false"; ?>,
				usingSpinner = <?php echo (config("using.spinner", true))? "true": "false"; ?>,
				permalinks = {capitalize: <?php echo (config("capitalize", true)) ? 'true': 'false'; ?>, separator: '<?php echo config("separator", true); ?>', single_var: '<?php echo config("single_var", true); ?>'},
				theme = '<?php echo config("theme", true); ?>';
			</script>
			<p><br /></p>

			<div style="text-align: center">
				<a class="small-8 small-centered large columns button save-config"><i class="fa fa-save"></i> <span class="txt">Save Changes</span></a>
			</div>
		</form>
	</div>

	<div class="content hide" id="panel-3">
		<a data-ng-click="proxyTest()" class="tiny button"><i class="fa fa-exchange"></i> Test Proxy</a>
		<pre id="log"></pre>
	</div>

	<div class="content hide" id="panel-4">
		<div class="callout panel">
			<strong>YOUR_MAIN_DOMAIN_DEFAULT_PASSWORD</strong> can be found at <code>maindomain.com/config.php</code>, if you changes default password, you can find password from default config at <code>/etc/mmengine.conf</code> via SSH.
		</div>

		<p><strong>FTP Access</strong><br />
		username: <code>agc</code>, password: <code>YOUR_MAIN_DOMAIN_DEFAULT_PASSWORD</code>
		</p>

		<p><strong>phpMyAdmin</strong><br />
		url: http://<?php echo domain(); ?>:8080<br />
		Auth username: <code>agc</code>, Auth password: <code>YOUR_MAIN_DOMAIN_DEFAULT_PASSWORD</code><br />
		username: <code>root</code>, password: <code>YOUR_MAIN_DOMAIN_DEFAULT_PASSWORD</code>
		</p>
	</div>

	<?php if(!config('installed')): ?>
	<div class="content hide" id="panel-5">
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