biigle.$viewModel("ananas-container",function(e){var n=biigle.$require("annotations.stores.images"),t=biigle.$require("ananas.annotation");t.shape=t.shape.name,new Vue({el:e,mixins:[biigle.$require("core.mixins.loader")],components:{sidebar:biigle.$require("annotations.components.sidebar"),sidebarTab:biigle.$require("core.components.sidebarTab"),annotationCanvas:biigle.$require("annotations.components.annotationCanvas")},data:{image:null,annotations:[]},methods:{setImageAndAnnotation:function(e){this.image=e,this.annotations.push(t)},focusAnnotation:function(){this.$refs.canvas.focusAnnotation(t,!0)},handleLoadingError:function(e){messages.danger(e)}},created:function(){this.startLoading(),n.fetchAndDrawImage(t.image_id).catch(this.handleLoadingError).then(this.setImageAndAnnotation).then(this.focusAnnotation).finally(this.finishLoading)}})}),biigle.$viewModel("create-ananas-form",function(e){new Vue({el:e,components:{labelTrees:biigle.$require("labelTrees.components.labelTrees")},data:{labelTrees:biigle.$require("ananas.labelTrees")},computed:{flatLabels:function(){var e=[];return this.labelTrees.forEach(function(n){Array.prototype.push.apply(e,n.labels)}),e},selectedLabels:function(){return this.flatLabels.filter(function(e){return e.selected})}},methods:{close:function(){window.close()}},created:function(){var e=biigle.$require("ananas.oldLabels");if(Array.isArray(e)){var n={};e.forEach(function(e){n[e]=null}),this.flatLabels.forEach(function(e){n.hasOwnProperty(e.id)&&Vue.set(e,"selected",!0)})}}})}),biigle.$require("annotations.components.annotationsTabPlugins").assistanceRequest={props:{annotations:{type:Array,required:!0},url:{type:String,required:!0}},computed:{selectedAnnotations:function(){return this.annotations.filter(function(e){return e.selected})},isDisabled:function(){return 1!==this.selectedAnnotations.length},href:function(){return this.url+this.selectedAnnotations[0].id}}};