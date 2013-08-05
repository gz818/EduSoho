define("arale/select/1.0.0/select",["arale/overlay/0.9.12/overlay","$","arale/position/1.0.0/position","arale/iframe-shim/1.0.0/iframe-shim","arale/widget/1.0.2/widget","arale/base/1.0.1/base","arale/class/1.0.0/class","arale/events/1.0.0/events","arale/widget/1.0.2/templatable","gallery/handlebars/1.0.0/handlebars"],function(e,t,n){function a(e,t){var n,r=[],i=e.options,s=i.length,o=!1;for(n=0;n<s;n++){var u,a={},f=i[n],l=["text","value","defaultSelected","selected"];for(u in l){var c=l[u];a[c]=f[c]}a.defaultSelected=f.defaultSelected?"true":"false",f.selected?(a.selected="true",o=!0):a.selected="false",r.push(a)}return o||(newModel[0].selected="true"),{select:r,classPrefix:t}}function f(e,t){var n,r,i,s,o=[],u=[];for(n=0,i=e.length;n<i;n++){var a=e[n];a.selected?(a.selected=a.defaultSelected="true",u.push(n)):a.selected=a.defaultSelected="false",o.push(a)}if(u.length>0){u.pop();for(r=0,s=u.length;r<s;r++)o[r].selected="false"}else o[0].selected="true";return{select:o,classPrefix:t}}function l(e,t){var n;return i.isNumeric(e)?n=e:typeof e=="string"?n=t.index(t.parent().find(e)):n=t.index(e),n}var r=e("arale/overlay/0.9.12/overlay"),i=e("$"),s=e("arale/widget/1.0.2/templatable"),o='<div class="{{classPrefix}}"> <ul class="{{classPrefix}}-content" data-role="content"> {{#each select}} <li data-role="item" class="{{../classPrefix}}-item" data-value="{{value}}" data-defaultSelected="{{defaultSelected}}" data-selected="{{selected}}">{{text}}</li> {{/each}} </ul> </div>',u=r.extend({Implements:s,attrs:{trigger:{value:null,getter:function(e){return i(e).eq(0)}},classPrefix:"ui-select",template:o,align:{baseXY:[0,"100%-1px"]},name:"",value:"",length:0,selectedIndex:-1,multiple:!1,disabled:!1,selectSource:null},events:{"click [data-role=item]":function(e){var t=i(e.currentTarget);this.select(t)},"mouseenter [data-role=item]":function(e){i(e.currentTarget).addClass(this.get("classPrefix")+"-hover")},"mouseleave [data-role=item]":function(e){i(e.currentTarget).removeClass(this.get("classPrefix")+"-hover")}},initAttrs:function(e,t){u.superclass.initAttrs.call(this,e,t);var n=this.get("trigger");if(n[0].tagName.toLowerCase()=="select"){var r=n.attr("name");r&&this.set("name",r),this.set("selectSource",n);var s='<a href="#" class="'+this.get("classPrefix")+'-trigger"></a>',o=i(s);this.set("trigger",o),n.after(o).hide(),this.model=a(n[0],this.get("classPrefix"))}else{var r=this.get("name");if(r){var l=i("input[name="+r+"]").eq(0);l[0]||(l=i('<input type="hidden" id="select-'+r+'" name="'+r+'" />').insertBefore(n)),this.set("selectSource",l)}this.model=f(this.model,this.get("classPrefix"))}},setup:function(){var e=this,t=this.get("trigger").on("click",{self:this},this._trigger_click).on("mouseenter",function(n){t.addClass(e.get("classPrefix")+"-trigger-hover")}).on("mouseleave",function(n){t.removeClass(e.get("classPrefix")+"-trigger-hover")});this.options=this.$("[data-role=content]").children(),this.select("[data-selected=true]"),this.set("length",this.options.length),this._tweakAlignDefaultValue(),this._blurHide(t),u.superclass.setup.call(this)},render:function(){return u.superclass.render.call(this),this._setTriggerWidth(),this},show:function(){return u.superclass.show.call(this),this._setPosition(),this},_setTriggerWidth:function(){var e=this.get("trigger"),t=this.element.outerWidth(),n=parseInt(e.css("padding-left"),10),r=parseInt(e.css("padding-right"),10),i=parseInt(e.css("border-left-width"),10),s=parseInt(e.css("border-right-width"),10);e.css("width",t-n-r-i-s)},_tweakAlignDefaultValue:function(){var e=this.get("align");e.baseElement._id==="VIEWPORT"&&(e.baseElement=this.get("trigger")),this.set("align",e)},_trigger_click:function(e){var t=e.data.self;e.preventDefault(),t.get("disabled")||t.show()},destroy:function(){this.element.remove(),u.superclass.destroy.call(this)},select:function(e){var t=l(e,this.options),n=this.get("selectedIndex");this.set("selectedIndex",t);if(n!==t){var r=this.options.eq(t);this.trigger("change",r)}return this.hide(),this},syncModel:function(e){return this.model=f(e,this.get("classPrefix")),this.renderPartial("[data-role=content]"),this.options=this.$("[data-role=content]").children(),this.set("length",this.options.length),this.set("selectedIndex",-1),this.set("value",""),this.select("[data-selected=true]"),this._setTriggerWidth(),this},getOption:function(e){var t=l(e,this.options);return this.options.eq(t)},addOption:function(e){var t=this.model.select;return t.push(e),this.syncModel(t),this},removeOption:function(e){var t=l(e,this.options),n=this.get("selectedIndex"),r=this.options.eq(t);return r.remove(),this.options=this.$("[data-role=content]").children(),this.set("length",this.options.length),t===n?this.set("selectedIndex",0):t<n&&this.set("selectedIndex",n-1),this},_onRenderSelectedIndex:function(e){if(e==-1)return;var t=this.options.eq(e),n=this.currentItem,r=t.attr("data-value");if(n&&t[0]==n[0])return;var i=this.get("selectSource");i&&(i[0].value=r),n&&n.attr("data-selected","false").removeClass(this.get("classPrefix")+"-selected"),t.attr("data-selected","true").addClass(this.get("classPrefix")+"-selected"),this.set("value",r);var s=this.get("trigger"),o=s.find("[data-role=trigger-content]");o.length?o.html(t.html()):s.html(t.html()),this.currentItem=t},_onRenderDisabled:function(e){var t=this.get("classPrefix")+"-disabled",n=this.get("trigger");n[e?"addClass":"removeClass"](t)}});n.exports=u});