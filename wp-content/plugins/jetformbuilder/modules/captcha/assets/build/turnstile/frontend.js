(()=>{"use strict";const{applyFilters:t}=JetPlugins.hooks,e=function(e){var n;if(e.parent)return;const r=e.getInput("_captcha_token"),o=e.getSubmit().getFormId(),i=r?.nodes?.[0]?.parentElement;let s=null!==(n=window?.JetFormBuilderCaptchaConfig?.[o])&&void 0!==n&&n;if(!i||!s)return;r.isVisible=()=>!0,s.callback=t=>{r.value.current=t},s=t("jet.fb.turnstile.options",s,e);const u=window.turnstile.render(i,s);e.getSubmit().submitter?.status?.watch?.((()=>{window.turnstile.reset(u),r.onClear()}))};window.jfbTurnstileOnLoad=function(){const{addAction:t}=JetPlugins.hooks;for(const t of Object.values(window.JetFormBuilder))t.hasOwnProperty("isObserved")&&e(t);t("jet.fb.observe.after","jet-form-builder/turnstile",e)}})();