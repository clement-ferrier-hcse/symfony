const { chromium } = require('playwright');
(async () => { const b = await chromium.launch();
for (const [name, vp, scheme] of [['desk-light',{width:1200,height:900},'light'],['phone-dark',{width:390,height:800},'dark']]) {
 const ctx = await b.newContext({ viewport: vp, colorScheme: scheme }); const p = await ctx.newPage();
 p.on('pageerror', e => console.log('PAGEERR', e.message)); p.on('console', m => { if (m.type()==='error') console.log('CONSOLE', m.text()); });
 await p.goto('file://' + process.argv[2]); await p.waitForTimeout(800);
 console.log(name, 'scrollW', await p.evaluate(()=>document.documentElement.scrollWidth), 'cells', await p.evaluate(()=>document.querySelectorAll('.cell[data-i]').length));
 await p.screenshot({ path: process.argv[3]+'/'+name+'.png', fullPage: false });
 if (name==='phone-dark') { await p.click('label[for=x], #mode-a', {force:true}).catch(()=>{}); await p.evaluate(()=>document.getElementById('sections').scrollIntoView()); await p.waitForTimeout(200); await p.screenshot({ path: process.argv[3]+'/phone-sections.png' }); console.log('after A mode cells', await p.evaluate(()=>document.querySelectorAll('.cell[data-i]').length)); }
}
await b.close(); })();
