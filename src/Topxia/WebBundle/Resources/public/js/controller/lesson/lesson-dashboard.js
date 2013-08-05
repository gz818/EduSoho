define(function(require, exports, module) {

	var Widget = require('widget'),
		Backbone = require('backbone'),
        VideoJS = require('video-js'),
        swfobject = require('swfobject'),
        Scrollbar = require('jquery.perfect-scrollbar');

	var Toolbar = require('./lesson-toolbar');

	var Dashboard = Widget.extend({

		_router: null,

		_toolbar: null,

		_lessons: [],

		events: {
			'click [data-role=next-lesson]': 'onNextLesson',
			'click [data-role=prev-lesson]': 'onPrevLesson',
			'click [data-role=finish-lesson]': 'onFinishLesson'
		},

		attrs: {
			courseId: null,
			courseUri: null,
			dashboardUri: null,
			lessonId: null
		},

		setup: function() {
			this._readAttrsFromData();
			this._initToolbar();
			this._initRouter();
			this._initListeners();
		},

		onNextLesson: function(e) {
			var next = this._getNextLessonId();
			if (next > 0) {
				this._router.navigate('lesson/' + next, {trigger: true});
			}
		},

		onPrevLesson: function(e) {
			var prev = this._getPrevLessonId();
			if (prev > 0) {
				this._router.navigate('lesson/' + prev, {trigger: true});
			}
		},

		onFinishLesson: function(e) {
			var $btn = this.element.find('[data-role=finish-lesson]');
			if ($btn.hasClass('disabled')) {
				this._onCancelLearnLesson();
			} else {
				this._onFinishLearnLesson();
			}
		},

		_startLesson: function() {
			var $btn = this.element.find('[data-role=finish-lesson]'),
				toolbar = this._toolbar,
				self = this;
			var url = '/course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/start';
			$.post(url, function(result) {
				if (result == true) {
					toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'learning'});
				}
			}, 'json');
		},

		_onFinishLearnLesson: function() {
			var $btn = this.element.find('[data-role=finish-lesson]'),
				toolbar = this._toolbar,
				self = this;
			var url = '/course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/finish';
			$.post(url, function(json) {
				$btn.addClass('disabled');
				toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'finished'});
			}, 'json');
		},

		_onCancelLearnLesson: function() {
			var $btn = this.element.find('[data-role=finish-lesson]'),
				toolbar = this._toolbar,
				self = this;
			var url = '/course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/cancel';
			$.post(url, function(json) {
				$btn.removeClass('disabled');
				toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'learning'});
			}, 'json');
		},

		_readAttrsFromData: function() {
			this.set('courseId', this.element.data('courseId'));
			this.set('courseUri', this.element.data('courseUri'));
			this.set('dashboardUri', this.element.data('dashboardUri'));
		},

		_initToolbar: function() {
	        this._toolbar = new Toolbar({
	            element: '#lesson-dashboard-toolbar',
	            activePlugins: ['lesson', 'question', 'note', 'material', 'quiz'],
	            courseId: this.get('courseId')
	        }).render();
		},

		_initRouter: function() {
			var that = this,
				DashboardRouter = Backbone.Router.extend({
	            routes: {
	                "lesson/:id": "lessonShow"
	            },

	            lessonShow: function(id) {
	                that.set('lessonId', id);
	            }
	        });

	        this._router = new DashboardRouter();
	        Backbone.history.start({pushState: false, root:this.get('dashboardUri')} );
		},

		_initListeners: function() {
			var that = this;
			this._toolbar.on('lessons_ready', function(lessons){
				that._lessons = lessons;
			});
		},

		_onChangeLessonId: function(id) {
            console.log('dashboard lessson id change:', id);
            if (!this._toolbar) {
            	return ;
            }
            console.log('xxx');
            this._toolbar.set('lessonId', id);

            var player = VideoJS("lesson-video-player");
            player.pause();
            swfobject.removeSWF('lesson-swf-player');

            this.element.find('[data-role=lesson-content]').hide();

			var that = this;
            $.get(this.get('courseUri') + '/lesson/' + id, function(lesson){
            	that._startLesson();
            	that.element.find('[data-role=lesson-title]').html(lesson.title);
            	that.element.find('[data-role=lesson-number]').html(lesson.number);
            	if (parseInt(lesson.chapterNumber) > 0) {
	            	that.element.find('[data-role=chapter-number]').html(lesson.chapterNumber).parent().show();
            	} else {
            		that.element.find('[data-role=chapter-number]').parent().hide();
            	}
            	if (lesson.type == 'video') {
            		if (lesson.media.source == 'self') {
			            player.dimensions('100%', '100%');
			            console.log(lesson.media.files[0].url);
			            player.src(lesson.media.files[0].url);
			            player.on('ended', function(){
			            	that._onFinishLearnLesson();
			            });
			            $("#lesson-video-content").show();
			            player.play();
            		} else {
            			$("#lesson-swf-content").html('<div id="lesson-swf-player"></div>');
            			swfobject.embedSWF(lesson.media.files[0].url, 'lesson-swf-player', '100%', '100%', "9.0.0");
            			$("#lesson-swf-content").show();
            		}

            	} else if (lesson.type == 'text') {
            		$("#lesson-text-content").find('.lesson-content-text-body').html(lesson.content);
            		$("#lesson-text-content").show();
            		$("#lesson-text-content").perfectScrollbar();
            	}
            }, 'json');

            $.get(this.get('courseUri') + '/lesson/' + id + '/learn/status', function(json) {
            	var $finishButton = that.element.find('[data-role=finish-lesson]');
            	if (json.status != 'finished') {
	            	$finishButton.removeClass('disabled');
            	} else {
            		$finishButton.addClass('disabled');
            	}
            }, 'json');

		},

		_getNextLessonId: function(e) {

			var index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
			if (index < 0) {
				return -1;
			}

			if (index + 1 >= this._lessons.length) {
				return -1;
			}

			return this._lessons[index+1];
		},

		_getPrevLessonId: function(e) {
			var index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
			if (index < 0) {
				return -1;
			}

			if (index == 0 ) {
				return -1;
			}

			return this._lessons[index-1];
		}

	});

	module.exports = Dashboard;

});