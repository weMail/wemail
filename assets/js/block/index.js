(()=>{"use strict";const e=window.wp.element,t=window.wp.blocks,a=window.wp.i18n,o=window.wp.components,r=window.wp.blockEditor;function n(){return n=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var o in a)Object.prototype.hasOwnProperty.call(a,o)&&(e[o]=a[o])}return e},n.apply(this,arguments)}function l(){let t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return(0,e.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 52.4 39.97"},(0,e.createElement)("g",{id:"Layer_2","data-name":"Layer 2"},(0,e.createElement)("g",{id:"Layer_1-2","data-name":"Layer 1"},(0,e.createElement)("path",n({},t.color?{fill:"#62ACDF"}:{},{class:"cls-1",d:"M11.66,7.76A9.27,9.27,0,0,0,2.2,6.64,9.07,9.07,0,0,0,0,8,11.67,11.67,0,0,1,11.3,0H52.4L27,19.64Z"})),(0,e.createElement)("path",n({},t.color?{fill:"#1256A1"}:{},{class:"cls-2",d:"M4.28,10.78a4.13,4.13,0,0,1,4.26.51l3.31,2.57L25.46,24.38a2.56,2.56,0,0,0,3.1,0L48.28,9.13l3.83-3v25C52.11,36,47.92,40,42.79,40H11.3C6.16,40,2,36,2,31.21v-17A3.68,3.68,0,0,1,4.28,10.78Z"})))))}(0,t.registerBlockType)("wemail/forms",{title:(0,a.__)("weMail"),description:(0,a.__)("Here you can add your weMail form."),category:"common",icon:l,keywords:[(0,a.__)("forms"),(0,a.__)("mail")],attributes:{formId:{type:"string"},shortcode:{type:"string"},isLoading:{type:"boolean",default:!1}},edit(t){function n(e){var a;t.setAttributes({shortcode:(a=e,`[wemail_form id="${a}"]`),formId:e,isLoading:!0})}function i(e){t.setAttributes({isLoading:!1});let a=e.target;a.removeAttribute("height"),a.height=a.contentWindow.document.body.offsetHeight}const s=[{label:(0,a.__)("Select your form"),value:"",disabled:!1}];return weMailData.forms.forEach((e=>{s.push({label:e.name,value:e.id})})),(0,e.createElement)("div",{className:"wemail-block"},(0,e.createElement)(r.InspectorControls,null,(0,e.createElement)(o.PanelBody,{title:(0,a.__)("Forms")},(0,e.createElement)(o.SelectControl,{label:(0,a.__)("Select your form"),value:t.attributes.formId,onChange:n,options:s}))),t.attributes.formId?function(t){const a=t.attributes.formId;return/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/.test(a)?(0,e.createElement)("div",{height:"500px",className:"wemail-block-form-preview"},(0,e.createElement)("div",{className:"wemail-block-overlay"}),(0,e.createElement)("iframe",{className:t.attributes.isLoading?"hide":"",onLoad:i,width:"100%",src:`${window.weMailData.siteUrl}/wp-admin/admin-ajax.php?action=wemail_preview&form_id=${encodeURIComponent(a)}`,frameBorder:"0",scrolling:"no"}),t.attributes.isLoading?(0,e.createElement)(o.Spinner,null):null):null}(t):function(t,r){return(0,e.createElement)(e.Fragment,null,(0,e.createElement)("div",{className:"icon"},l({color:!0})),(0,e.createElement)("h2",{className:"title"},(0,a.__)("weMail Form")),(0,e.createElement)(o.SelectControl,{value:t.attributes.formId,onChange:n,label:(0,a.__)("Forms"),options:r}))}(t,s))},save:t=>(0,e.createElement)(e.RawHTML,null,t.attributes.shortcode)})})();