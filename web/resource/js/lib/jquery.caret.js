jQuery.fn.extend({selectContents:function(){$(this).each(function(e){var t,n,c,o,a=this;(c=a.ownerDocument)&&(o=c.defaultView)&&void 0!==o.getSelection&&void 0!==c.createRange&&(t=window.getSelection())&&void 0!==t.removeAllRanges?((n=c.createRange()).selectNode(a),0==e&&t.removeAllRanges(),t.addRange(n)):document.body&&void 0!==document.body.createTextRange&&(n=document.body.createTextRange())&&(n.moveToElementText(a),n.select())})},setCaret:function(){var e;document.selection&&(e=function(){$(this).get(0).caretPos=document.selection.createRange().duplicate()},$(this).click(e).select(e).keyup(e))},insertAtCaret:function(e){var t,n,c,o=$(this).get(0);document.all&&o.createTextRange&&o.caretPos?(n=o.caretPos).text=""==n.text.charAt(n.text.length-1)?e+"":e:o.setSelectionRange?(t=o.selectionStart,c=o.selectionEnd,n=o.value.substring(0,t),c=o.value.substring(c),o.value=n+e+c,o.focus(),c=e.length,o.setSelectionRange(t+c,t+c),o.blur()):o.value+=e}});