(function($){
'use strict';
var boot=window.MS_SECTION_BOOT||{};
var previewTimer=null;
var previewPayload=null;
var previewRoots=[];

function pathParts(path){return String(path||'').split('.').filter(Boolean);}
function setPath(obj,path,val){var p=pathParts(path),r=obj;p.forEach(function(k,i){var key=/^\d+$/.test(k)?parseInt(k,10):k;if(i===p.length-1){r[key]=val;}else{if(typeof r[key]!=='object'||r[key]===null)r[key]=/^\d+$/.test(p[i+1])?[]:{};r=r[key];}});}
function getPath(obj,path,def){var r=obj;for(const k of pathParts(path)){if(r==null||typeof r!=='object'||!(k in r))return def;r=r[k];}return r;}
function decodeHtml(v){var t=document.createElement('textarea');t.innerHTML=String(v??'');return t.value;}
function itemBySlug(data,slug){var a=Array.isArray(data.items)?data.items:[];return a.find(x=>String(x.slug)===String(slug))||null;}
function itemValue(data,slug,path,def){var item=itemBySlug(data,slug);return item?getPath(item,path,def):def;}
function assetUrl(v){v=String(v||'').trim();if(!v)return'';if(/^(?:https?:|data:|\/\/)/i.test(v))return v;var base=String($('#url_sitio').val()||'').replace(/\/+$/,'')+'/';if(v.indexOf('assets/')===0)return base+v.replace(/^\/+/, '');return base+String($('#carpeta_uploads').val()||'').replace(/^\/+|\/+$/g,'')+'/'+v.replace(/^\/+/, '');}

function initEditors(root){$(root||document).find('.ms-editor-html').each(function(){var $t=$(this);if($t.next('.note-editor').length)return;if(typeof $t.summernote!=='function'){return;}$t.summernote({height:parseInt($t.data('height')||90,10),toolbar:[['style',['bold','italic','underline','clear']],['para',['ul','ol']],['insert',['link']],['view',['codeview']]],callbacks:{onChange:function(){schedulePreview();markDirty();}}});});}
function collect(){var data={};$('.ms-campo').each(function(){var $e=$(this),path=$e.data('path');if(!path)return;var v=$e.val();if($e.data('kind')==='number'){v=v===''?0:Number(v);}setPath(data,path,v);});$('.ms-editor-html').each(function(){var $e=$(this),path=$e.data('path');if(!path)return;setPath(data,path,(typeof $e.summernote==='function'&&$e.next('.note-editor').length)?$e.summernote('code'):$e.val());});return data;}
function markDirty(){$('.ms-save-state').text('Cambios sin guardar').addClass('text-warning').removeClass('text-success');}

function cssProp(k){return({font_size:'fontSize',font_weight:'fontWeight',text_align:'textAlign',line_height:'lineHeight',letter_spacing:'letterSpacing',text_transform:'textTransform',background_color:'backgroundColor',border_color:'borderColor',border_radius:'borderRadius',glow_color:'--cms-glow',icon_color:'--cms-icon-color',panel_background:'background',panel_border:'borderColor'})[k]||k.replace(/_([a-z])/g,(_,c)=>c.toUpperCase());}
function applyStyle(el,obj){if(!el||!obj||typeof obj!=='object')return;Object.keys(obj).forEach(k=>{var v=obj[k];if(v===null||typeof v==='object')return;var p=cssProp(k);if(p.indexOf('--')===0)el.style.setProperty(p,v);else try{el.style[p]=String(v);}catch(e){}});if(obj.glow_color)el.style.boxShadow='0 0 22px '+obj.glow_color;if(obj.icon_color){var im=el.querySelector('img');if(im)im.style.filter='none';}}

function animationVars(cfg){var i=Number(cfg.intensidad||20),type=String(cfg.tipo||'fadeIn');var from={opacity:0},to={opacity:1};if(type==='fadeUp'||type==='staggerUp')from.y=i;if(type==='fadeDown')from.y=-i;if(type==='slideLeft')from.x=-i;if(type==='slideRight')from.x=i;if(type==='scaleIn')from.scale=.86;if(type==='float'){from.y=-i/2;to.y=i/2;to.repeat=-1;to.yoyo=true;}if(type==='pulse'){from.scale=.96;to.scale=1.04;to.repeat=-1;to.yoyo=true;}return{from:from,to:to};}
function runAnimation(root,el,cfg){if(!el||!cfg||Number(cfg.activo)===0||String(cfg.tipo)==='none')return;if(typeof gsap==='undefined')return;gsap.killTweensOf(el);var v=animationVars(cfg);v.to.duration=Number(cfg.duracion||.5);v.to.delay=Number(cfg.delay||0);v.to.ease='power2.out';gsap.fromTo(el,v.from,v.to);}

function applyGroupStyles(root,data){
  var scope=boot.scope;
  if(scope==='header'){
    var m=getPath(data,'estilos.menu',{}),nav=root.querySelector('.main-nav');if(nav){nav.querySelectorAll('a').forEach(a=>{a.style.color=m.color||'';a.style.fontSize=m.font_size||'';a.style.fontWeight=m.font_weight||'';});var ac=nav.querySelector('.is-active');if(ac){ac.style.color=m.color_activo||'';ac.style.background=m.fondo_activo||'';}}
  }
  if(scope==='demo'){
    var t=getPath(data,'estilos.tabs',{});root.querySelectorAll('.module-tab').forEach(x=>{x.style.color=t.color||'';x.style.background=t.background||'';x.style.borderColor=t.border_color||'';});var a=root.querySelector('.module-tab.is-active');if(a){a.style.color=t.active_color||'';a.style.background=t.active_background||'';a.style.borderColor=t.active_border||'';}
    var p=getPath(data,'estilos.pasos',{});root.querySelectorAll('.demo-steps span').forEach(x=>x.style.color=p.color||'');var pa=root.querySelector('.demo-steps .is-active');if(pa){pa.style.color=p.active_color||'';var b=pa.querySelector('b');if(b){b.style.background=p.active_background||'';b.style.borderColor=p.active_background||'';}}
  }
  if(scope==='encomienda'){
    var l=getPath(data,'estilos.linea',{}),line=root.querySelector('.track-line');if(line&&l.color)line.style.background=l.color;root.querySelectorAll('.track-step.is-done i').forEach(x=>{if(l.completado){x.style.borderColor=l.completado;x.style.color=l.completado;}});
  }
  if(scope==='contacto'){
    var f=getPath(data,'estilos.formulario',{}),form=root.querySelector('.approved-contact-form');if(form){form.querySelectorAll('.field-label').forEach(x=>x.style.color=f.label_color||'');form.querySelectorAll('input,textarea').forEach(x=>{x.style.background=f.input_background||'';x.style.color=f.input_color||'';x.style.borderColor=f.border_color||'';});form.querySelectorAll('.required-mark').forEach(x=>x.style.color=f.required_color||'');var btn=form.querySelector('button[type="submit"]');if(btn){btn.style.background=f.button_background||'';btn.style.color=f.button_color||'';}}
  }
}

function updateItemElement(root,el,item){if(!item)return;var title=el.querySelector('strong, span:not(.module-node)');if(title&&item.titulo!==undefined)title.innerHTML=decodeHtml(item.titulo).replace(/\n/g,'<br>');var img=el.querySelector('img');if(img&&item.icono)img.src=assetUrl(item.icono);}
function updateRow(el,row){if(!el||!row)return;var b=el.querySelector('b');if(b&&row.monto!==undefined)b.innerHTML=decodeHtml(row.monto);var spans=el.querySelectorAll('span,b,small');if(row.unidad!==undefined){var s=el.querySelector('span');if(s)s.innerHTML=decodeHtml(row.unidad);if(b)b.innerHTML=decodeHtml(row.estado||'');return;}if(row.nombre!==undefined){var bb=el.querySelector('b'),sm=el.querySelector('small');if(bb)bb.innerHTML=decodeHtml(row.nombre);if(sm)sm.innerHTML=decodeHtml(row.rol||'');return;}if(row.concepto!==undefined){var icon=el.querySelector('i');var amount=el.querySelector('b');Array.from(el.childNodes).filter(n=>n.nodeType===3).forEach(n=>n.remove());if(icon)icon.insertAdjacentText('afterend',decodeHtml(row.concepto)+' ');if(amount)amount.innerHTML=decodeHtml(row.monto||'');}}

function applyData(root,data){
  root.querySelectorAll('[data-cms]').forEach(el=>{var p=el.getAttribute('data-cms').replace(new RegExp('^'+boot.scope+'\\.'),'');var v=getPath(data,p,null);if(v!==null&&v!==undefined)el.innerHTML=decodeHtml(v);});
  root.querySelectorAll('[data-cms-image]').forEach(el=>{var p=el.getAttribute('data-cms-image').replace(new RegExp('^'+boot.scope+'\\.'),'');var v=getPath(data,p,'');if(v)el.src=assetUrl(v);});
  root.querySelectorAll('[data-cms-link]').forEach(el=>{var p=el.getAttribute('data-cms-link').replace(new RegExp('^'+boot.scope+'\\.'),'');var v=getPath(data,p,'');if(v)el.href=decodeHtml(v);});
  root.querySelectorAll('[data-cms-item]').forEach(el=>{var raw=el.getAttribute('data-cms-item'),slug=raw.split('.').pop(),item=itemBySlug(data,slug);updateItemElement(root,el,item);});
  root.querySelectorAll('[data-cms-item-field]').forEach(el=>{var raw=el.getAttribute('data-cms-item-field').split(':'),v=itemValue(data,raw[0],raw[1],null);if(v!==null&&v!==undefined)el.innerHTML=decodeHtml(v);});
  root.querySelectorAll('[data-cms-item-image]').forEach(el=>{var raw=el.getAttribute('data-cms-item-image').split(':'),v=itemValue(data,raw[0],raw[1],'');if(v)el.src=assetUrl(v);});
  root.querySelectorAll('[data-cms-item-row]').forEach(el=>{var raw=el.getAttribute('data-cms-item-row').split(':'),row=itemValue(data,raw[0],raw[1],null);updateRow(el,row);});
  root.querySelectorAll('[data-cms-style]').forEach(el=>applyStyle(el,getPath(data,el.getAttribute('data-cms-style'),{})));
  root.querySelectorAll('[data-cms-item-style]').forEach(el=>{var raw=el.getAttribute('data-cms-item-style').split(':'),st=itemValue(data,raw[0],raw[1],{});if(boot.scope==='demo'){var panel=el.querySelector('.v5-hud,.v7-hud-card')||el;if(st.panel_background)panel.style.background=st.panel_background;if(st.panel_border)panel.style.borderColor=st.panel_border;if(st.accent){el.querySelectorAll('.widget-action,.v7-shipping-ticket em,.v7-report-actions button .bi').forEach(x=>{x.style.setProperty('--cms-accent',st.accent);if(x.matches('.widget-action,.v7-shipping-ticket em'))x.style.background=st.accent;});}}else{applyStyle(el,st);}if(st&&st.border_color){var before=el.querySelector('.module-frame path');if(before)before.style.stroke=st.border_color;el.style.borderColor=st.border_color;}if(st&&st.glow_color)el.style.boxShadow='0 0 22px '+st.glow_color;});
  applyGroupStyles(root,data);
  // Form labels/placeholders/required
  var form=getPath(data,'formulario',{});root.querySelectorAll('[data-cms-form-field]').forEach(control=>{var key=control.getAttribute('data-cms-form-field'),c=form[key]||{};if(c.placeholder!==undefined)control.placeholder=decodeHtml(c.placeholder);control.required=Number(c.requerido)===1;var lab=root.querySelector('[data-cms-form-label="'+key+'"] .field-label');if(lab&&c.label!==undefined)lab.innerHTML=decodeHtml(c.label)+(Number(c.requerido)===1?' <span class="required-mark">*</span>':'');});
  var map=root.querySelector('[data-cms-map]');if(map){var q=getPath(data,map.getAttribute('data-cms-map'),'');if(q)map.src='https://www.google.com/maps?q='+encodeURIComponent(decodeHtml(q))+'&output=embed';}
  // Animations
  root.querySelectorAll('[data-cms-animation]').forEach(el=>runAnimation(root,el,getPath(data,el.getAttribute('data-cms-animation'),{})));
  root.querySelectorAll('[data-cms-item-animation]').forEach(el=>{var raw=el.getAttribute('data-cms-item-animation').split(':'),cfg=itemValue(data,raw[0],raw[1],{});runAnimation(root,el,cfg);});
}

function initSectionInteractions(root){
  // Demo seats
  root.querySelectorAll('.seat-grid').forEach(grid=>{if(grid.children.length)return;for(let i=1;i<=20;i++){let b=document.createElement('button');b.type='button';if([3,7,10,13,18].includes(i))b.classList.add('is-occupied');if(i===14)b.classList.add('is-selected');b.addEventListener('click',()=>{grid.querySelectorAll('button').forEach(x=>x.classList.remove('is-selected'));b.classList.add('is-selected');var l=root.querySelector('#seat-label');if(l)l.textContent=i;var d=root.querySelector('#proof-detail');if(d)d.textContent='Asiento '+i;});grid.appendChild(b);}});
  root.querySelectorAll('.module-tab').forEach(tab=>tab.addEventListener('click',()=>{var id=tab.dataset.module;root.querySelectorAll('.module-tab').forEach(x=>{x.classList.toggle('is-active',x===tab);x.setAttribute('aria-selected',x===tab?'true':'false');});root.querySelectorAll('[data-scene]').forEach(x=>x.classList.toggle('is-visible',x.dataset.scene===id));var stage=root.querySelector('.demo-v5-stage');if(stage)stage.dataset.active=id;}));
  root.querySelectorAll('[data-info-module]').forEach(btn=>btn.addEventListener('click',()=>{var id=btn.dataset.infoModule,src=root.querySelector('.module-modal-source[data-module="'+id+'"]'),modal=root.querySelector('#module-modal'),ct=root.querySelector('#module-modal-container');if(src&&modal&&ct){ct.innerHTML=src.innerHTML;modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');}}));
  root.querySelectorAll('[data-close-module]').forEach(x=>x.addEventListener('click',()=>{var modal=root.querySelector('#module-modal');if(modal){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');}}));
  root.querySelectorAll('.track-step').forEach(step=>step.addEventListener('click',()=>{root.querySelectorAll('.track-step').forEach(x=>x.classList.remove('is-active'));step.classList.add('is-active');var out=root.querySelector('#tracking-status');if(out)out.textContent='Estado: '+step.dataset.status;}));
}

function setupScale(host,shadow){var canvas=shadow.querySelector('.cms-site-section-canvas');if(!canvas)return;function fit(){var w=host.clientWidth||1199,scale=Math.min(1,w/1199);canvas.style.transform='scale('+scale+')';var h=canvas.scrollHeight*scale;host.style.height=Math.max(160,h)+'px';}fit();if(typeof ResizeObserver!=='undefined'){
    var ro=new ResizeObserver(fit);
    ro.observe(host);
}
setTimeout(fit,250);
setTimeout(fit,800);}
function renderHost(host,payload){if(!host)return;host.innerHTML='';var shadow=host.shadowRoot||host.attachShadow({mode:'open'});shadow.innerHTML='';(payload.imports||[]).forEach(url=>{var l=document.createElement('link');l.rel='stylesheet';l.href=url;shadow.appendChild(l);});var s=document.createElement('style');s.textContent=payload.css+`\n.cms-site-section-canvas{position:relative;margin:0 auto;} .cms-site-section-canvas *{box-sizing:border-box;}`;shadow.appendChild(s);var canvas=document.createElement('div');canvas.className='cms-site-section-canvas';canvas.innerHTML=payload.html;shadow.appendChild(canvas);initSectionInteractions(shadow);applyData(shadow,collect());setupScale(host,shadow);previewRoots.push(shadow);}
function previewMessage(title,detail,type){var host=document.getElementById('cmsPreviewHost');if(!host)return;var icon=type==='error'?'bi-exclamation-triangle':'bi-hourglass-split';host.innerHTML='<div class="ms-preview-loading-state"><i class="bi '+icon+' fs-2"></i><strong>'+String(title||'')+'</strong><small>'+String(detail||'')+'</small></div>';}
function loadPreview(){previewRoots=[];$('#previewStatus').text('Leyendo HTML de la sección…');previewMessage('Cargando vista previa…','Leyendo únicamente la sección '+String(boot.slug_seccion||'')+'.','loading');$.ajax({url:'preview-seccion.php',data:{cod_seccion:boot.cod_seccion},type:'get',dataType:'json',cache:false,success:function(r){if(!r||!r.ok){var msg=(r&&r.message)?r.message:'No se pudo cargar.';$('#previewStatus').text(msg);previewMessage('No se pudo cargar el preview',msg,'error');return;}previewPayload=r;renderHost(document.getElementById('cmsPreviewHost'),r);$('#previewStatus').text('Sección conectada al HTML real');},error:function(xhr){var detail='HTTP '+xhr.status+'. '+(xhr.responseText?String(xhr.responseText).replace(/<[^>]+>/g,' ').slice(0,240):'Revisa preview-seccion.php y la URL del sitio.');$('#previewStatus').text('Error '+xhr.status+' cargando preview');previewMessage('Error cargando el preview',detail,'error');}});}
function postPreview(){clearTimeout(previewTimer);previewTimer=setTimeout(function(){var d=collect();previewRoots.forEach(r=>applyData(r,d));$('#previewStatus').text('Vista actualizada');},100);}
function schedulePreview(){postPreview();}

function uploadFile($field,file){var url=String($('#url_sitio').val()||'').replace(/\/+$/,'')+'/admin-upload.php';var token=$('#token_sitio').val();var folder=$('#carpeta_uploads').val();var fd=new FormData();fd.append('file',file);fd.append('token',token);fd.append('folder',folder);var xhr=new XMLHttpRequest();var $bar=$field.find('.ms-upload-progress span'),$status=$field.find('.ms-upload-status');xhr.open('POST',url,true);xhr.upload.onprogress=function(e){if(e.lengthComputable){var p=Math.round(e.loaded/e.total*100);$bar.css('width',p+'%');$status.text('Cargando '+p+'%…');}};xhr.onreadystatechange=function(){if(xhr.readyState!==4)return;if(xhr.status>=200&&xhr.status<300){try{var d=JSON.parse(xhr.responseText);if(d.ok){$field.find('.ms-media-value').val(d.file).trigger('input');$field.find('.ms-media-preview').html(`<img src="${d.url}" alt="">`);$bar.css('width','100%');$status.text('Completado');msToast('Archivo subido.');schedulePreview();}else throw new Error(d.message||'Error');}catch(err){$status.text('Error');msToast(err.message,'danger');}}else{$status.text('Error '+xhr.status);msToast('No se pudo subir el archivo. Revisa admin-upload.php y el token.','danger');}};xhr.send(fd);}
function bindUploads(){$(document).on('click','.ms-trigger-upload',function(e){e.preventDefault();$(this).closest('.ms-media-field').find('.ms-file-input').trigger('click');});$(document).on('change','.ms-file-input',function(){var f=this.files&&this.files[0];if(f)uploadFile($(this).closest('.ms-media-field'),f);this.value='';});$(document).on('dragover','.ms-dropzone',function(e){e.preventDefault();$(this).addClass('drag-over');});$(document).on('dragleave','.ms-dropzone',function(){$(this).removeClass('drag-over');});$(document).on('drop','.ms-dropzone',function(e){e.preventDefault();$(this).removeClass('drag-over');var f=e.originalEvent.dataTransfer.files[0];if(f)uploadFile($(this).closest('.ms-media-field'),f);});$(document).on('click','.ms-open-icon-library',function(){$(this).closest('.ms-icon-field').find('.ms-icon-library').toggleClass('d-none');});$(document).on('click','.ms-icon-option',function(){var $field=$(this).closest('.ms-media-field'),v=$(this).data('value'),src=$(this).find('img').attr('src');$field.find('.ms-media-value').val(v).trigger('input');$field.find('.ms-media-preview').html(`<img src="${src}" alt="">`);$field.find('.ms-icon-library').addClass('d-none');});}
function bindColors(){$(document).on('input','.ms-color-picker',function(){var p=$(this).data('target-path'),v=$(this).val();$('.ms-campo[data-path="'+p.replace(/"/g,'\\"')+'"]').val(v).trigger('input');});}
function bindSimpleLists(){$(document).on('click','.ms-add-simple',function(){var $box=$(this).closest('[data-array-path]'),path=$box.data('array-path'),idx=$box.find('.ms-simple-item').length;var h=`<div class="input-group mb-2 ms-simple-item"><span class="input-group-text">${idx+1}</span><input class="form-control ms-campo" data-path="${path}.${idx}" value=""><button class="btn btn-outline-danger ms-remove-row" type="button"><i class="bi bi-trash"></i></button></div>`;$box.find('.ms-simple-list').append(h);markDirty();});$(document).on('click','.ms-remove-row',function(){var $box=$(this).closest('[data-array-path]');$(this).closest('.ms-simple-item').remove();var path=$box.data('array-path');$box.find('.ms-simple-item').each(function(i){$(this).find('.input-group-text').text(i+1);$(this).find('.ms-campo').attr('data-path',path+'.'+i).data('path',path+'.'+i);});markDirty();schedulePreview();});}
function save(){var params={action:'guardarSeccionCms',cod_seccion:$('#cod_seccion').val(),nom_seccion:$('#nom_seccion').val(),carpeta_uploads:$('#carpeta_uploads').val(),contenido:JSON.stringify(collect())};$('.ms-save-section').prop('disabled',true).html('<span class="spinner-border spinner-border-sm me-1"></span> Guardando…');$.ajax({data:params,url:'ajax.php',type:'post',dataType:'json',success:function(r){if(r.ok){$('.ms-save-state').text('Guardado').removeClass('text-warning').addClass('text-success');msToast('Sección guardada.');}else msToast(r.mensaje||'No se pudo guardar.','danger');},error:function(xhr){msToast('Error '+xhr.status+' al guardar.','danger');},complete:function(){$('.ms-save-section').prop('disabled',false).html('<i class="bi bi-floppy me-1"></i> Guardar cambios');}});}
function fullscreen(){if(!previewPayload)return;var host=document.getElementById('cmsPreviewFullscreenHost');previewRoots=previewRoots.filter(r=>r.host!==host);renderHost(host,previewPayload);bootstrap.Modal.getOrCreateInstance(document.getElementById('previewFullscreen')).show();setTimeout(()=>postPreview(),120);}

$(function(){

    try{
        initEditors(document);
        bindUploads();
        bindColors();
        bindSimpleLists();

        $(document).on(
            'input change',
            '.ms-campo,#nom_seccion,#carpeta_uploads',
            function(){
                markDirty();
                schedulePreview();
            }
        );

        $('.ms-save-section').on('click',save);
        $('.ms-refresh-preview').on('click',loadPreview);
        $('.ms-fullscreen-preview').on('click',fullscreen);

        loadPreview();

    }catch(err){

        previewMessage(
            'Error inicializando el editor',
            err && err.message ? err.message : String(err),
            'error'
        );

        $('#previewStatus').text('Error de inicialización');
    }

});
})(jQuery);