var App = angular.module('App', [])
.directive('navPanel', function () {
	return {
		restrict: 'C',
		link: function (scope, el, attr) {
			el.bind('click', function (e) {
				var parent = el.parent(), tab = $('#' + attr.rel);
				parent.siblings().removeClass('active');
				tab.siblings().removeClass('active');

				parent.addClass('active');
				tab.addClass('active');
			});
		}
	}
})
.directive('saveConfig', ['$timeout', function (t) {
	return {
		restrict: 'C',
		link: function (scope, el, attr) {
			var json = {};
			el.bind('click', function (e) {
				e.preventDefault();

				if (! el.hasClass('disabled')) {
					var icon = el.children('i'), txt = el.children('.txt'), originalText = txt.text();

					el.addClass('disabled');
					icon.removeClass('fa-save').addClass('fa-refresh');
					txt.text("Loading...");

					_($('#config').serializeArray()).each(function (item, index) {
						if (item.name.indexOf('config[') !== -1) {
							var key = item.name.replace(/config\[(.*)\]/igm, "$1"), val = item.value;
							json[key] = val;
						}
					});


					json.logo_ext = "";
					json.logo_data = "";

					if (json.logo_tmp !== 'no') {
						var base64_img = $("#canvas").get(0).getContext('2d').canvas.toDataURL().split(';');
						json.logo_ext = base64_img[0].replace("data:image/", "");
						json.logo_data = base64_img[1].split(",")[1];
					}

					$.ajax({
						url: './index.php',
						type: 'POST',
						data: {type: 'config', config: json},
						success: function (response) {
							if (response.status === 200) {
								icon.removeClass('fa-refresh').addClass('fa-check');
								txt.text("Saved");

								t(function () {
									icon.removeClass('fa-check').addClass('fa-save');
									txt.text(originalText);
									el.removeClass('disabled');
								}, 1000);
							}
							else if (response.status === 302) {
								window.location = './logout.php';
							}
						}
					});
				}
			});
		}
	}
}])
.controller('Main', ['$scope', '$rootScope', '$timeout', function (s, r, t) {
	s.icon = ["angellist","area-chart","at","bell-slash","bell-slash-o","bicycle","binoculars","birthday-cake","bus","calculator","cc","cc-amex","cc-discover","cc-mastercard","cc-paypal","cc-stripe","cc-visa","copyright","eyedropper","futbol-o","google-wallet","ils","ioxhost","lastfm","lastfm-square","line-chart","meanpath","newspaper-o","paint-brush","paypal","pie-chart","plug","slideshare","toggle-off","toggle-on","trash","tty","twitch","wifi","yelp","adjust","anchor","archive","arrows","arrows-h","arrows-v","asterisk","ban","bar-chart","barcode","bars","beer","bell","bell-o","bolt","bomb","book","bookmark","bookmark-o","briefcase","bug","building","building-o","bullhorn","bullseye","calendar","calendar-o","camera","camera-retro","car","caret-square-o-down","caret-square-o-left","caret-square-o-right","caret-square-o-up","certificate","check","check-circle","check-circle-o","check-square","check-square-o","child","circle","circle-o","circle-o-notch","circle-thin","clock-o","cloud","cloud-download","cloud-upload","code","code-fork","coffee","cog","cogs","comment","comment-o","comments","comments-o","compass","credit-card","crop","crosshairs","cube","cubes","cutlery","database","desktop","dot-circle-o","download","ellipsis-h","ellipsis-v","envelope","envelope-o","envelope-square","eraser","exchange","exclamation","exclamation-circle","exclamation-triangle","external-link","external-link-square","eye","eye-slash","fax","female","fighter-jet","file-archive-o","file-audio-o","file-code-o","file-excel-o","file-image-o","file-pdf-o","file-powerpoint-o","file-video-o","file-word-o","film","filter","fire","fire-extinguisher","flag","flag-checkered","flag-o","flask","folder","folder-o","folder-open","folder-open-o","frown-o","gamepad","gavel","gift","glass","globe","graduation-cap","hdd-o","headphones","heart","heart-o","history","home","inbox","info","info-circle","key","keyboard-o","language","laptop","leaf","lemon-o","level-down","level-up","life-ring","lightbulb-o","location-arrow","lock","magic","magnet","male","map-marker","meh-o","microphone","microphone-slash","minus","minus-circle","minus-square","minus-square-o","mobile","money","moon-o","music","paper-plane","paper-plane-o","paw","pencil","pencil-square","pencil-square-o","phone","phone-square","picture-o","plane","plus","plus-circle","plus-square","plus-square-o","power-off","print","puzzle-piece","qrcode","question","question-circle","quote-left","quote-right","random","recycle","refresh","reply","reply-all","retweet","road","rocket","rss","rss-square","search","search-minus","search-plus","share","share-alt","share-alt-square","share-square","share-square-o","shield","shopping-cart","sign-in","sign-out","signal","sitemap","sliders","smile-o","sort","sort-alpha-asc","sort-alpha-desc","sort-amount-asc","sort-amount-desc","sort-asc","sort-desc","sort-numeric-asc","sort-numeric-desc","space-shuttle","spinner","spoon","square","square-o","star","star-half","star-half-o","star-o","suitcase","sun-o","tablet","tachometer","tag","tags","tasks","taxi","terminal","thumb-tack","thumbs-down","thumbs-o-down","thumbs-o-up","thumbs-up","ticket","times","times-circle","times-circle-o","tint","trash-o","tree","trophy","truck","umbrella","university","unlock","unlock-alt","upload","user","users","video-camera","volume-down","volume-off","volume-up","wheelchair","wrench","file","file-o","file-text","file-text-o","btc","eur","gbp","inr","jpy","krw","rub","try","usd","align-center","align-justify","align-left","align-right","bold","chain-broken","clipboard","columns","files-o","floppy-o","font","header","indent","italic","link","list","list-alt","list-ol","list-ul","outdent","paperclip","paragraph","repeat","scissors","strikethrough","subscript","superscript","table","text-height","text-width","th","th-large","th-list","underline","undo","angle-double-down","angle-double-left","angle-double-right","angle-double-up","angle-down","angle-left","angle-right","angle-up","arrow-circle-down","arrow-circle-left","arrow-circle-o-down","arrow-circle-o-left","arrow-circle-o-right","arrow-circle-o-up","arrow-circle-right","arrow-circle-up","arrow-down","arrow-left","arrow-right","arrow-up","arrows-alt","caret-down","caret-left","caret-right","caret-up","chevron-circle-down","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-down","chevron-left","chevron-right","chevron-up","hand-o-down","hand-o-left","hand-o-right","hand-o-up","long-arrow-down","long-arrow-left","long-arrow-right","long-arrow-up","backward","compress","eject","expand","fast-backward","fast-forward","forward","pause","play","play-circle","play-circle-o","step-backward","step-forward","stop","youtube-play","adn","android","apple","behance","behance-square","bitbucket","bitbucket-square","codepen","css3","delicious","deviantart","digg","dribbble","dropbox","drupal","empire","facebook","facebook-square","flickr","foursquare","git","git-square","github","github-alt","github-square","gittip","google","google-plus","google-plus-square","hacker-news","html5","instagram","joomla","jsfiddle","linkedin","linkedin-square","linux","maxcdn","openid","pagelines","pied-piper","pied-piper-alt","pinterest","pinterest-square","qq","rebel","reddit","reddit-square","renren","skype","slack","soundcloud","spotify","stack-exchange","stack-overflow","steam","steam-square","stumbleupon","stumbleupon-circle","tencent-weibo","trello","tumblr","tumblr-square","twitter","twitter-square","vimeo-square","vine","vk","weibo","weixin","windows","wordpress","xing","xing-square","yahoo","youtube","youtube-square","ambulance","h-square","hospital-o","medkit","stethoscope","user-md"];
	s.engineType = window.engineType;
	s.usingSpinner = window.usingSpinner;
	s.showPassword = {password: false};
	s.databases = (typeof databases !== "undefined")? databases: {};
	s.databases.status = false;
	s.togglePassword = function (e) {
		var password = $(e.target).parent().prev();
		if (s.showPassword[password.attr('name')] || s.showPassword[password.attr('name')] === undefined) {
			password.attr('type', 'text');
			s.showPassword[password.attr('name')] = false;
		} else {
			password.attr('type', 'password');
			s.showPassword[password.attr('name')] = true;
		}
	};

	s.checkDbProgress = false;
	s.installDbProgress = false;
	s.checkDatabase = function () {
		s.checkDbProgress = true;
		$.ajax({
			url: 'index.php',
			type: 'POST',
			dataType: 'JSON',
			data: {
				type: 'checkdb',
				db_username: s.databases.username,
				db_password: s.databases.password,
				db_name: s.databases.name
			},
			success: function (response, status) {
				if (response.status === 200) {
					s.databases.status = true;
				} else {
					s.databases.status = false;
				}
				s.checkDbProgress = false;
				if(! s.$$phase) s.$apply();
			}
		});
	};

	s.installDatabase = function () {
		s.installDbProgress = true;
		$.ajax({
			url: 'index.php',
			type: 'POST',
			dataType: 'JSON',
			data: {
				type: 'installdb',
				db_username: s.databases.username,
				db_password: s.databases.password,
				db_name: s.databases.name
			},
			success: function (response, status) {
				if (response.status === 200) {
					window.location = 'index.php';
				}
			}
		});
	};

	s.addLog = function (txt) {
		$('#log').append('<div>['+ new Date() +'] : '+ txt +'</div>');
	};

	s.proxyTest = function () {
		s.addLog ('Request connection...');
		$.ajax({
			url: 'proxy.php',
			success: function (response, status) {
				if (response === '') {
					s.addLog('Tor isn\'t alive');
				} else {
					s.addLog('Current Proxy IP Address: ' + response.match(/(?:(?:2[0-4]\d|25[0-5]|1\d{2}|[1-9]?\d)\.){3}(?:2[0-4]\d|25[0-5]|1\d{2}|[1-9]?\d)/)[0]);
				}
			}
		});
	};

	s.render = function () {
		if(! w2ui.table) {
			$('#table').w2grid({ 
				name: 'table', 
				show: { 
					toolbar: true,
					toolbarColumns: false,
					toolbarSearch: false,
					toolbarEdit: false,
					toolbarDelete: true,
					toolbarSave: false
				},
				toolbar: {
					items: [
						{ type: 'break' },
						{ type: 'button', id: 'clear', caption: 'Clear All', img: 'icon-delete', hint: 'Clear all keywords' }
					],
					onClick: function (target, data) {

						if(s.onProgress) {
							return false;
						}

						var selection = w2ui.table.getSelection();

						switch(target) {
							case 'clear':
								w2confirm("Clear keywords?", function (answer) {
									if(answer === 'Yes') {
										w2ui.table.clear();
										if (! s.$$phase) s.$apply();
									}
								})
							break;
						}
					}
				},
				columns: [
					{ field: 'keyword', sortable: true, caption: 'Keyword', size: '50%' },
					{ field: 'status', caption: '', size: '30px', attr: 'align="center"', render: function (record) {
						var status = record.status;

						if (status === true) {
							return '<i class="fa fa-check" title="Success"></i>';
						} 
						else if(status === false) {
							return '<i class="fa fa-x" title="Failed"></i>';
						}
						else {
							return '<i class="fa fa-clock-o" title="Waiting"></i>';
						}
					}}
				],
				records: []
			});
		} else {
			$('#table').w2render('table');
		}
	};
	s.render();

	r.onProgress = false;
	r.onPrepare = false;
	r.newCategory = '';
	r.keywords = '';
	r.selectedCategory = 'new';
	r.selectedIcon = 'ellipsis-h';
	r.expandIcons = false;

	s.toggleExpandIcon = function () {
		if (r.expandIcons) r.expandIcons = false;
		else r.expandIcons = true;
	};

	s.changeCategory = function () {
		r.selectedIcon = $('option[value="'+ r.selectedCategory +'"]').attr('data-icon');
	};

	s.unPrepare = function (e) {
		if (! $(e.target).hasClass('disabled')) {
			r.onPrepare = false;
			if (!r.$$phase) r.$apply();
		}
	};
	s.prepareKeywords = function () {
		var keywords = r.keywords.split("\n").map(function (item) {
			return $.trim(S(item).slugify().humanize().s.toLowerCase());
		}).filter(function (item) {
			return item !== '';
		});

		if (keywords.length > 0) {
			if (r.selectedCategory === 'new' && r.newCategory === '') {
				alert('Please enter category');
				return;
			}

			r.onPrepare = true;
			t(function () {
				var list = [];
				$.each(keywords, function (index, item) {
					if(_.where(w2ui.table.records, {keyword: item}).length === 0) {
						list.push({
							keyword: item,
							status: '',
							recid: Date.now() + index
						})
					}
				})
				w2ui.table.add(list);
				w2ui.table.save();
				s.render();

			})
		}
	};

	s.noRecords = function () {
		if (_(w2ui.table.records).size() > 0) {
			return false;
		}

		return true;
	};

	s.stopQueue = function () {
		r.onProgress = false;
		r.onPrepare = false;
		w2ui.table.clear();
		if(! r.$$phase) r.$apply();
	};
	s.insertKeyword = function (e) {
		if (! $(e.target).hasClass('disabled')) {
			r.onProgress = true;
			if(! r.$$phase) r.$apply();

			var category = (r.selectedCategory === 'new') ? r.newCategory: r.selectedCategory;
			var q = async.queue(function (task, callback) {
				$.ajax({
					url: 'index.php',
					type: 'POST',
					data: {
						category: S(category).slugify().humanize().s,
						icon: r.selectedIcon,
						keyword: S(task.keyword).slugify().humanize().s.toLowerCase(),
						index: task.index,
						type: 'insert'
					},
					dataType: 'JSON',
					success: function (response, status) {
						console.log(task.id);
						if (response.status === 200) {
							_.findWhere(w2ui.table.records, {recid: task.id}).status = true;
						}
						else {
							_.findWhere(w2ui.table.records, {recid: task.id}).status = false;
						}

						w2ui.table.save();
						callback();
					}
				})
			});
			q.drain = function () {
				s.stopQueue();
				t(function () {
					window.location = './index.php';
				}, 1000);
			};

			_(w2ui.table.records).each(function (item, index) {
				q.push({
					id: item.recid,
					keyword: item.keyword,
					index: index
				});
			});
		}
	};

	s.setConfig = function (type) {
		if (type === 'pdf') {
			$('[name="config[type]"').val('pdf');
			$('[name="config[theme]"').find('[value="dir-pdf"]').attr('selected', 'selected');
			$('[name="config[separator]"').val('_');
			$('[name="config[capitalize]"').val('true');
		} else {
			$('[name="config[type]"').val('html');
			$('[name="config[theme]"').find('[value="pdf-default"]').attr('selected', 'selected');
			$('[name="config[separator]"').val('-');
			$('[name="config[capitalize]"').val('false');
		}
	};
}]);


(function ($) {
	$(document).ready(function () {
		var canvas = $("#canvas");
		var context = canvas.get(0).getContext("2d");
		if (canvas.attr('data-url') !== '') {
			var img = new Image();
			img.onload = function() {
				canvas.attr('width', img.width);
				canvas.attr('height', img.height);
				context.drawImage(img, 0, 0);
			};
			img.src = canvas.attr('data-url');
		}

		$('#logo').on("change", function () {
			if ( this.files && this.files[0] ) {
				var fr = new FileReader();
				fr.onload = function(e) {
					var img = new Image();
					img.onload = function() {

						if (img.width < 400 && img.height < 250) {
							canvas.attr('width', img.width);
							canvas.attr('height', img.height);
							context.drawImage(img, 0, 0);
						} else {
							canvas.attr('width', 400);
							canvas.attr('height', 250);
							context.drawImage(img, 0, 0, 400, 250);
						}
						
					};
					img.src = e.target.result;
				};
				fr.readAsDataURL(this.files[0] );
				$("#logo_tmp").val(Date.now());
			} else {
				$("#logo_tmp").val("no");
			}
		});

		$('#icons').removeAttr('style');
	})
})(jQuery);