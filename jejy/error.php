<script>
(function () {
  function el(q, r = document) { return r.querySelector(q); }
  function all(q, r = document) { return Array.from(r.querySelectorAll(q)); }
  function isShown(node) {
    if (!node) return false;
    const s = getComputedStyle(node);
    return s.display !== 'none' && s.visibility !== 'hidden';
  }

  function ensureBanner() {
    const wrap = el('#welcome-panel .qs-wrap') || el('#welcome-panel');
    let b = el('#qsError');
    if (!b) {
      b = document.createElement('div');
      b.id = 'qsError';
      b.className = 'qs-error-banner';
      b.setAttribute('role', 'alert');
      b.setAttribute('aria-live', 'polite');
      b.innerHTML = '<h3>A selection is required.</h3><p>To proceed, please fill in or correct the required field(s).</p>';
      wrap?.insertBefore(b, wrap.firstChild);
    }
    return b;
  }

  function clearErrors() {
    el('#qsError')?.classList.remove('show');
    all('#welcome-panel .is-invalid').forEach(n => n.classList.remove('is-invalid'));
    all('#welcome-panel .fi-error-text').forEach(n => n.remove());
  }

  // clear error on a specific host
  function clearErrorOn(host) {
    if (!host) return;
    host.classList.remove('is-invalid');
    const e = host.querySelector('.fi-error-text');
    if (e) e.remove();
  }

  function addError(host, msg) {
    if (!host) host = el('#welcome-panel');
    host.classList.add('is-invalid');
    if (host.querySelector(':scope > .fi-error-text')) return host;
    const m = document.createElement('div');
    m.className = 'fi-error-text';
    m.textContent = msg || 'This field is required.';
    host.appendChild(m);
    return host;
  }

  function radioVal(name) {
    const r = el(`input[name="${name}"]:checked`);
    return r ? r.value : '';
  }
  function clearRadioGroup(name) {
    all(`input[name="${name}"]`).forEach(r => r.checked = false);
  }
  function setRadioValue(name, val) {
    if (!val) { clearRadioGroup(name); return; }
    const r = el(`input[name="${name}"][value="${val}"]`);
    if (r) r.checked = true;
  }
  function selectValue(sel) {
    if (!sel) return '';
    const idx = sel.selectedIndex;
    if (idx < 0) return '';
    const opt = sel.options[idx];
    if (!opt || opt.disabled || opt.value === '') return '';
    return opt.value || opt.text || '';
  }
  function blockHasCheckedRadios(block) {
    if (!block) return false;
    return !!block.querySelector('input[type="radio"]:checked, input[type="checkbox"]:checked');
  }

  // ===== PER-STATUS MEMORY =====
  const statusMemory = {};
  const dependentFields = ['spouse_in_canada', 'spouseFile', 'children'];
  let lastStatus = '';

  function getCurrentStatus() {
    const sel = el('#marital_status_select');
    const msSel = selectValue(sel);
    const msRad = radioVal('marital_status');
    return msRad || msSel || '';
  }

  function saveCurrentStatusAnswers() {
    if (!lastStatus) return;
    const store = {};
    dependentFields.forEach(name => {
      store[name] = radioVal(name); // '' if none
    });
    statusMemory[lastStatus] = store;
  }

  function restoreStatusAnswers(newStatus) {
    const saved = statusMemory[newStatus];
    dependentFields.forEach(name => {
      if (saved && saved[name]) {
        setRadioValue(name, saved[name]);
      } else {
        clearRadioGroup(name);
      }
    });
  }

  // show/hide children (and clear errors when hidden)
  function updateChildrenVisibility(status) {
    const outer = el('#children-block');
    const inner = outer ? outer.querySelector('.yn-group') : null;
    if (!outer) return;

    if (status && status !== 'Single') {
      outer.style.display = '';
    } else {
      outer.style.display = 'none';
      // clear errors on both wrappers
      clearErrorOn(outer);
      if (inner) clearErrorOn(inner);
    }
  }

  // clear errors on dependent groups when status changes
  function clearDependentErrors() {
    // spouse in Canada
    const spouseInner = el('.yn-group input[name="spouse_in_canada"]')?.closest('.yn-group');
    // spouse file
    const spouseFileInner = el('#spouse-file-block .yn-group');
    // children
    const childrenOuter = el('#children-block');
    const childrenInner = childrenOuter ? childrenOuter.querySelector('.yn-group') : null;

    clearErrorOn(spouseInner);
    clearErrorOn(spouseFileInner);
    clearErrorOn(childrenOuter);
    clearErrorOn(childrenInner);

    // hide banner if nothing else invalid
    const panel = el('#welcome-panel');
    if (panel && !panel.querySelector('.is-invalid')) {
      el('#qsError')?.classList.remove('show');
    }
  }

  function onMaritalStatusChange(newStatus) {
    // save previous status answers
    saveCurrentStatusAnswers();
    // switch status
    lastStatus = newStatus;
    // restore answers for this status
    restoreStatusAnswers(newStatus);
    // show/hide blocks
    updateChildrenVisibility(newStatus);
    // clear old errors from previous status
    clearDependentErrors();
  }

  // ===== VALIDATION =====
  function validateWelcome() {
    clearErrors(); // start clean
    ensureBanner();
    const errs = [];

    const selMS = el('#marital_status_select');
    const msSel = selectValue(selMS);
    const msRad = radioVal('marital_status');
    const ms = msRad || msSel;

    if (!ms) {
      errs.push(addError(selMS?.closest('.qs-block') || selMS || el('.qs-choicegrid'), 'Please select your marital status.'));
    } else if (msSel) {
      // keep radios in sync
      const r = el(`input[name="marital_status"][value="${msSel}"]`);
      if (r && !r.checked) r.checked = true;
    }

    const marriedLike = (ms === 'Married' || ms === 'Common Law');
    const needsSDW = (ms === 'Separated' || ms === 'Divorced' || ms === 'Widowed');
    const singleLike = (ms === 'Single');

    // marriage/common-law date
    const marryBlock = el('#status-date-block');
    if (marriedLike && marryBlock) {
      const d = el('#status_date');
      if (!d || !d.value.trim()) {
        errs.push(addError(marryBlock, ms === 'Common Law'
          ? 'Please enter your common-law status date.'
          : 'Please enter your date of marriage.'
        ));
      }
    }

    // separated/divorced/widowed date
    const sdwBlock = el('#status-date-sdw-block');
    if (needsSDW && sdwBlock) {
      const sdw = el('#status_date_sdw');
      const lbl = el('#status-date-sdw-label')?.textContent || 'date';
      if (!sdw || !sdw.value.trim()) {
        errs.push(addError(sdwBlock, `Please enter ${lbl.toLowerCase()}.`));
      }
    }

    // spouse in Canada
    if (marriedLike) {
      const spouseYN = el('.yn-group input[name="spouse_in_canada"]')?.closest('.yn-group');
      if (spouseYN && !blockHasCheckedRadios(spouseYN)) {
        errs.push(addError(spouseYN, 'Please choose Yes or No.'));
      }
    }

    // spouse wants to file
    const inCanadaYes = el('#spouse_in_canada_yes')?.checked;
    const spouseFileYN = el('#spouse-file-block .yn-group');
    if (marriedLike && inCanadaYes && spouseFileYN && isShown(spouseFileYN)) {
      if (!blockHasCheckedRadios(spouseFileYN)) {
        errs.push(addError(spouseFileYN, 'Please indicate if your spouse will file.'));
      }
    }

    // children – validate for ALL except single
    const childrenOuter = el('#children-block');
    const childrenInner = childrenOuter ? childrenOuter.querySelector('.yn-group') : null;
    const childrenHost = childrenInner || childrenOuter;
    if (!singleLike && childrenHost && isShown(childrenHost)) {
      if (!blockHasCheckedRadios(childrenHost)) {
        errs.push(addError(childrenHost, 'Please select if you have children.'));
      }
    }

    if (errs.length) {
      el('#qsError')?.classList.add('show');
      const first = errs[0];
      if (first) {
        const focusable = first.querySelector('input, select, button, textarea');
        if (focusable?.focus) focusable.focus();
        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return false;
    }
    return true;
  }

  // live clear
  function attachLiveCleanup() {
    const panel = el('#welcome-panel');
    if (!panel) return;
    function maybeClear(e) {
      const host = e.target.closest('.is-invalid');
      if (!host) return;
      host.classList.remove('is-invalid');
      host.querySelector('.fi-error-text')?.remove();
      if (!panel.querySelector('.is-invalid')) el('#qsError')?.classList.remove('show');
    }
    panel.addEventListener('input', maybeClear, true);
    panel.addEventListener('change', maybeClear, true);
  }

  function wireButton() {
    const btn = el('#welcome-panel #qs-continue');
    if (!btn) return;

    // capture = true so this runs BEFORE the main app's listener
    btn.addEventListener('click', function (ev) {
      // keep UI in sync if your main app exposes this
      if (window.App?.refreshWelcomeBlocks) {
        window.App.refreshWelcomeBlocks();
      }

      const ok = validateWelcome();
      if (!ok) {
        // block navigation ONLY when invalid
        ev.preventDefault();
        ev.stopPropagation();
        ev.stopImmediatePropagation(); // ← this stops the other click handler
        return;
      }
      // if valid: do nothing — let the main script handle the panel switch
    }, true);
  }

  function wireMaritalStatus() {
    const sel = el('#marital_status_select');
    if (sel) {
      sel.addEventListener('change', function () {
        onMaritalStatusChange(this.value);
      });
    }
    all('input[name="marital_status"]').forEach(r => {
      r.addEventListener('change', function () {
        const s = el('#marital_status_select');
        if (s) s.value = this.value;
        onMaritalStatusChange(this.value);
      });
    });

    // init
    const initStatus = getCurrentStatus();
    if (initStatus) {
      lastStatus = initStatus;
      updateChildrenVisibility(initStatus);
      if (!statusMemory[initStatus]) statusMemory[initStatus] = {};
    }
  }

  ensureBanner();
  attachLiveCleanup();
  wireButton();
  wireMaritalStatus();
})();
</script>





<!-- PERSONAL ERROR 2 SCRIPT -->

<script>
(function(){
  // ---------- tiny helpers ----------
  const $  = (q, r=document)=>r.querySelector(q);
  const $$ = (q, r=document)=>Array.from(r.querySelectorAll(q));

  const panel = document.querySelector('.pi-main[data-panel="personal"]');
  if (!panel) return;

  // ---------- banner ----------
  function ensureBanner(){
    let b = panel.querySelector('#personalError');
    if (!b){
      b = document.createElement('div');
      b.id = 'personalError';
      b.className = 'qs-error-banner';
      b.setAttribute('role','alert');
      b.setAttribute('aria-live','polite');
      b.innerHTML = `
        <h3>A selection is required.</h3>
        <p>To proceed, please fill in or correct the required field(s).</p>
      `;
      panel.insertBefore(b, panel.firstElementChild);
    }
    return b;
  }
  function showBanner(){ ensureBanner().classList.add('show'); }
  function hideBanner(){ panel.querySelector('#personalError')?.classList.remove('show'); }

  // ---------- error helpers ----------
  function clearErrors(){
    hideBanner();
    $$('.is-invalid', panel).forEach(n=>n.classList.remove('is-invalid'));
    $$('.fi-error-text', panel).forEach(n=>n.remove());
  }

  // put error under the right container
  function errorUnder(anchor, msg){
    if (!anchor) return null;

    // special case: DOB -> we want the error on the fi-grid so calendar icon doesn't drop
    if (anchor.id === 'dob') {
      const grid = anchor.closest('.fi-grid') || anchor.closest('.fi-group') || anchor.parentElement;
      if (!grid) return null;
      grid.classList.add('is-invalid');
      if (!grid.querySelector(':scope > .fi-error-text')) {
        const m = document.createElement('div');
        m.className = 'fi-error-text';
        m.textContent = msg || 'This field is required.';
        grid.appendChild(m);
      }
      return grid;
    }

    const host = anchor.closest('.fi-group, .yn-group, .qs-block') || anchor.parentElement || anchor;
    host.classList.add('is-invalid');

    if (!host.querySelector(':scope > .fi-error-text')) {
      const m = document.createElement('div');
      m.className = 'fi-error-text';
      m.textContent = msg || 'This field is required.';
      host.appendChild(m);
    }
    return host;
  }

  // ---------- value helpers ----------
  const get = id => panel.querySelector('#' + id);
  const val = id => (get(id)?.value || '').trim();

  // ---------- validators ----------
  const twoLetters = v => /^[A-Za-z][A-Za-z\-' ]+$/.test(v) && v.replace(/[^A-Za-z]/g,'').length >= 2;

  const MONTHS = {
    jan:1, january:1,
    feb:2, february:2,
    mar:3, march:3,
    apr:4, april:4,
    may:5,
    jun:6, june:6,
    jul:7, july:7,
    aug:8, august:8,
    sep:9, sept:9, september:9,
    oct:10, october:10,
    nov:11, november:11,
    dec:12, december:12
  };

  function isValidDate(y, m, d){
    const dt = new Date(y, m-1, d);
    return dt.getFullYear() === y && dt.getMonth() === m-1 && dt.getDate() === d;
  }

  // accepts: 05 | Jan | 2016, 5 Jan 2016, 05-Jan-2016, 05/Jan/2016
  function parseDobDisplay(v){
    if (!v) return null;
    // split on any non-word-ish separator
    const parts = v.trim().split(/[\s|\/\-]+/).filter(Boolean);
    if (parts.length !== 3) return null;

    const d = parseInt(parts[0], 10);
    const mStr = parts[1].toLowerCase();
    const y = parseInt(parts[2], 10);

    const m = MONTHS[mStr];
    if (!m || isNaN(d) || isNaN(y)) return null;
    if (!isValidDate(y, m, d)) return null;

    return { d, m, y };
  }

  const sinOk    = v => /^\d{9}$/.test(v.replace(/\D/g,''));
  const postalOk = v => /^[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d$/.test(v.trim());
  const phoneOk  = v => v.replace(/\D/g,'').length >= 10;
  const emailOk  = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

  // ---------- main validate ----------
  function validatePersonal(){
    clearErrors();
    const errs = [];

    // names
    if (!twoLetters(val('first_name')))
      errs.push(errorUnder(get('first_name'), 'Your first name needs at least two letters.'));
    if (!twoLetters(val('last_name')))
      errs.push(errorUnder(get('last_name'), 'Your last name needs at least two letters.'));

    // DOB (DD | MMM | YYYY)
    const dobInput = get('dob');
    const dobParsed = parseDobDisplay(val('dob'));
    if (!dobParsed){
      errs.push(errorUnder(dobInput, 'Enter date as DD | MMM | YYYY.'));
    } else {
      // normalize to your display style
      const day = String(dobParsed.d).padStart(2,'0');
      // find 3-letter month
      const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const monthLabel = monthNames[dobParsed.m - 1];
      dobInput.value = `${day} | ${monthLabel} | ${dobParsed.y}`;
    }

    // SIN
    const sinRaw = val('sin').replace(/\D/g,'');
    if (!sinOk(sinRaw))
      errs.push(errorUnder(get('sin'), 'Enter a 9-digit SIN, numbers only.'));

    // gender
    if (!panel.querySelector('input[name="gender"]:checked')){
      const anyGenderInput = panel.querySelector('input[name="gender"]');
      errs.push(errorUnder(anyGenderInput, 'Please select a gender.'));
    }

    // address
    if (!val('street'))   errs.push(errorUnder(get('street'),   'Street is required.'));
    if (!val('city'))     errs.push(errorUnder(get('city'),     'City is required.'));
    if (!val('province')) errs.push(errorUnder(get('province'), 'State/Province is required.'));
    if (!postalOk(val('postal'))) errs.push(errorUnder(get('postal'), 'Enter a valid postal code (e.g., A1A 1A1).'));
    if (!val('country'))  errs.push(errorUnder(get('country'),  'Country is required.'));

    // contact
    if (!phoneOk(val('phone'))) errs.push(errorUnder(get('phone'), 'Enter a valid phone number (10+ digits).'));
    if (!emailOk(val('email'))) errs.push(errorUnder(get('email'), 'Enter a valid email address.'));

    if (errs.length){
      showBanner();
      const first = errs[0];
      if (first){
        const focusable = first.querySelector('input, select, textarea, button') || first;
        focusable?.focus?.();
        first.scrollIntoView({ behavior:'smooth', block:'center' });
      }
      return false;
    }
    return true;
  }

// ------- Live cleanup when fixing fields -------
panel.addEventListener('input', (e)=>{
  // special case: DOB error sits on the grid
  if (e.target.id === 'dob') {
    const dobGrid = panel.querySelector('.fi-grid.is-invalid:has(#dob)');
    if (dobGrid) {
      dobGrid.classList.remove('is-invalid');
      dobGrid.querySelector('.fi-error-text')?.remove();
      if (!panel.querySelector('.is-invalid')) hideBanner();
    }
    return;
  }

  const host = e.target.closest('.fi-group, .yn-group, .qs-block, .fi-grid');
  if (host?.classList.contains('is-invalid')){
    host.classList.remove('is-invalid');
    host.querySelector('.fi-error-text')?.remove();
    if (!panel.querySelector('.is-invalid')) hideBanner();
  }
}, true);

panel.addEventListener('change', (e)=>{
  if (e.target.id === 'dob') {
    const dobGrid = panel.querySelector('.fi-grid.is-invalid:has(#dob)');
    if (dobGrid) {
      dobGrid.classList.remove('is-invalid');
      dobGrid.querySelector('.fi-error-text')?.remove();
      if (!panel.querySelector('.is-invalid')) hideBanner();
    }
    return;
  }

  const host = e.target.closest('.fi-group, .yn-group, .qs-block, .fi-grid');
  if (host?.classList.contains('is-invalid')){
    host.classList.remove('is-invalid');
    host.querySelector('.fi-error-text')?.remove();
    if (!panel.querySelector('.is-invalid')) hideBanner();
  }
}, true);

  // ---------- hook Continue ----------
  const nextBtn = panel.querySelector('.continue-btn[data-goto="next"]');
  if (nextBtn){
    nextBtn.addEventListener('click', (ev)=>{
      if (!validatePersonal()){
        ev.preventDefault();
        ev.stopPropagation();
        ev.stopImmediatePropagation();
      }
    }, true);
  }

  // create banner hidden
  ensureBanner();
})();


</script>


<!-- TAX ERROR 3 SCRIPT -->
<script>
(function () {
  const panel = document.querySelector('.pi-main[data-panel="tax"]');
  if (!panel) return;

  const $  = (q, r=document) => r.querySelector(q);
  const $$ = (q, r=document) => Array.from(r.querySelectorAll(q));

  /* ----------------------------------------------------------
     ERROR BANNER
     ---------------------------------------------------------- */
  function ensureBanner() {
    let b = panel.querySelector('#taxError');
    if (!b) {
      b = document.createElement('div');
      b.id = 'taxError';
      b.className = 'qs-error-banner';
      b.setAttribute('role','alert');
      b.setAttribute('aria-live','polite');
      b.innerHTML = `
        <h3>A selection is required.</h3>
        <p>To proceed, please fill in or correct the required field(s).</p>
      `;
      panel.insertBefore(b, panel.firstElementChild);
    }
    return b;
  }
  function showBanner(){ ensureBanner().classList.add('show'); }
  function hideBanner(){ panel.querySelector('#taxError')?.classList.remove('show'); }

  /* ----------------------------------------------------------
     DATE HELPERS
     ---------------------------------------------------------- */
  const MONTHS = {
    jan:1,january:1,
    feb:2,february:2,
    mar:3,march:3,
    apr:4,april:4,
    may:5,
    jun:6,june:6,
    jul:7,july:7,
    aug:8,august:8,
    sep:9,sept:9,september:9,
    oct:10,october:10,
    nov:11,november:11,
    dec:12,december:12
  };
  function isValidDate(y,m,d){
    const dt = new Date(y,m-1,d);
    return dt.getFullYear()===y && dt.getMonth()===m-1 && dt.getDate()===d;
  }
  function parseDobDisplay(v){
    if (!v) return null;
    const parts = v.trim().split(/[\s|\/\-]+/).filter(Boolean);
    if (parts.length !== 3) return null;
    const d = parseInt(parts[0],10);
    const mStr = parts[1].toLowerCase();
    const y = parseInt(parts[2],10);
    const m = MONTHS[mStr];
    if (!m || isNaN(d) || isNaN(y)) return null;
    if (!isValidDate(y,m,d)) return null;
    return {d,m,y};
  }
  function normalizeDateInput(inputEl){
    const parsed = parseDobDisplay(inputEl.value);
    if (!parsed) return false;
    const day = String(parsed.d).padStart(2,'0');
    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const monthLabel = monthNames[parsed.m - 1];
    inputEl.value = `${day} | ${monthLabel} | ${parsed.y}`;
    const bindSel = inputEl.getAttribute('data-bind');
    if (bindSel) {
      const hidden = panel.querySelector(bindSel);
      if (hidden) hidden.value = `${parsed.y}-${String(parsed.m).padStart(2,'0')}-${day}`;
    }
    return true;
  }

  /* ----------------------------------------------------------
     CLEAR ERRORS
     ---------------------------------------------------------- */
  function clearErrors() {
    hideBanner();
    $$('.is-invalid', panel).forEach(n => n.classList.remove('is-invalid'));
    $$('.fi-error-text', panel).forEach(n => n.remove());
  }

  /* ----------------------------------------------------------
     ERROR PLACEMENT
     ---------------------------------------------------------- */
  function errorUnder(anchor, msg) {
    if (!anchor) return null;

    // DATE inputs: absolute so layout doesn't shift
    if (
      anchor.classList.contains('dob-input') ||
      anchor.id === 'entry_date_display' ||
      anchor.id === 'moved_date_display' ||
      anchor.id === 'first_home_purchase_display'
    ) {
      const group = anchor.closest('.fi-group');
      if (group) {
        group.classList.add('is-invalid', 'has-date-error');
        if (!group.querySelector('.fi-error-text')) {
          const m = document.createElement('div');
          m.className = 'fi-error-text fi-error-abs';
          m.textContent = msg || 'Enter date as DD | MMM | YYYY.';
          group.appendChild(m);
        }
        return group;
      }
    }

    // RENT block: whole block
    if (anchor.id === 'rent-addresses' || (anchor.closest && anchor.closest('#rent-addresses'))) {
      const wrap = anchor.id === 'rent-addresses' ? anchor : anchor.closest('#rent-addresses');
      wrap.classList.add('is-invalid');
      if (!wrap.querySelector(':scope > .fi-error-text')) {
        const m = document.createElement('div');
        m.className = 'fi-error-text';
        m.textContent = msg || 'Please add at least one rent address.';
        wrap.appendChild(m);
      }
      return wrap;
    }

    // radio yes/no
    if (anchor.type === 'radio') {
      const ynGroup = anchor.closest('.yn-group');
      if (ynGroup) {
        ynGroup.classList.add('is-invalid');
        if (!ynGroup.querySelector('.fi-error-text')) {
          const m = document.createElement('div');
          m.className = 'fi-error-text';
          m.textContent = msg || 'Please select Yes or No.';
          ynGroup.appendChild(m);
        }
        return ynGroup;
      }
    }

    // normal fields
    const group = anchor.closest('.fi-group');
    if (group) {
      group.classList.add('is-invalid');
      if (!group.querySelector('.fi-error-text')) {
        const m = document.createElement('div');
        m.className = 'fi-error-text';
        m.textContent = msg || 'This field is required.';
        group.appendChild(m);
      }
      return group;
    }

    // fallback
    const host = anchor.closest('.qs-block') || anchor.parentElement;
    host.classList.add('is-invalid');
    if (!host.querySelector(':scope > .fi-error-text')) {
      const m = document.createElement('div');
      m.className = 'fi-error-text';
      m.textContent = msg || 'This field is required.';
      host.appendChild(m);
    }
    return host;
  }

  /* ----------------------------------------------------------
     PANEL PARTS WE TOGGLE
     ---------------------------------------------------------- */
  const priorSection     = $('#prior-customer-section', panel);
  const firsttimeDetails = $('#firsttime-details', panel);
  const wiWrapper        = $('#wi-wrapper', panel);
  const movedSection     = $('#moved-section', panel);
  const movexpDetails    = $('#movexp-details', panel);
  const fthbDetails      = $('#fthb-details', panel);
  const ownersGrid       = $('#owners-grid', panel);
  const ownersWrap       = $('#owners-wrap', panel);
  const rentBlock        = $('#rent-block', panel);
  const claimRentBlock   = $('#claim-rent-block', panel);
  const rentAddresses    = $('#rent-addresses', panel);

  function toggleFirstTime(){
    const sel = panel.querySelector('input[name="first_time"]:checked');
    if (!sel) {
      priorSection?.classList.add('is-hidden');
      firsttimeDetails?.classList.add('is-hidden');
      wiWrapper?.classList.add('is-hidden');
      return;
    }
    if (sel.value === 'no') {
      priorSection?.classList.remove('is-hidden');
      firsttimeDetails?.classList.add('is-hidden');
      wiWrapper?.classList.add('is-hidden');
    } else {
      priorSection?.classList.add('is-hidden');
      firsttimeDetails?.classList.remove('is-hidden');
      const entry = $('#entry_date_display', panel);
      if (entry && entry.value.trim() !== '') {
        wiWrapper?.classList.remove('is-hidden');
      }
    }
  }
  function toggleMoved(){
    const sel = panel.querySelector('input[name="moved_province"]:checked');
    if (sel && sel.value === 'yes') movedSection?.classList.remove('is-hidden');
    else movedSection?.classList.add('is-hidden');
  }
  function toggleMovExp(){
    const sel = panel.querySelector('input[name="moving_expenses_claim"]:checked');
    if (sel && sel.value === 'yes') movexpDetails?.classList.remove('is-hidden');
    else movexpDetails?.classList.add('is-hidden');
  }
  function toggleFTHB(){
    const sel = panel.querySelector('input[name="first_home_buyer"]:checked');
    if (sel && sel.value === 'yes') fthbDetails?.classList.remove('is-hidden');
    else fthbDetails?.classList.add('is-hidden');
  }
function toggleClaimFull(){
  const sel = panel.querySelector('input[name="claim_full"]:checked');
  const wrap = $('#owners-wrap', panel);     // inner div with the input
  const rentBlock = $('#rent-block', panel); // "Are you living on Rent?"

  if (sel && sel.value === 'no') {
    // show extra field
    wrap?.classList.remove('is-hidden');
    wrap?.setAttribute('aria-hidden','false');

    // keep the outer grid visible (do NOT hide #owners-grid)
    // add the gap before rent
    rentBlock?.classList.add('rent-gap');
  } else {
    // hide only the inner field
    wrap?.classList.add('is-hidden');
    wrap?.setAttribute('aria-hidden','true');

    // remove gap before rent
    rentBlock?.classList.remove('rent-gap');
  }
}

  function toggleRent(){
    const onRent = panel.querySelector('input[name="onRent"]:checked');
    if (onRent && onRent.value === 'yes') {
      claimRentBlock.style.display = '';
      const claim = panel.querySelector('input[name="claimRent"]:checked');
      if (claim && claim.value === 'yes') {
        rentAddresses.style.display = '';
      } else {
        rentAddresses.style.display = 'none';
      }
    } else {
      claimRentBlock.style.display = 'none';
      rentAddresses.style.display = 'none';
    }
  }

  /* ----------------------------------------------------------
     VALIDATION
     ---------------------------------------------------------- */
  function validateTaxPanel() {
    clearErrors();
    const errs = [];

    // 1) first time?
    const firstSel = panel.querySelector('input[name="first_time"]:checked');
    if (!firstSel) {
      errs.push(errorUnder($('#first_yes', panel), 'Please select Yes or No.'));
    } else if (firstSel.value === 'no') {
      if (!panel.querySelector('input[name="paragon_prior"]:checked')) {
        errs.push(errorUnder($('#paragon_yes', panel), 'Please select Yes or No.'));
      }
      const years = $('#return_years', panel);
      if (years && !years.value.trim()) {
        errs.push(errorUnder(years, 'Please enter the year(s) you want to file.'));
      }
    } else if (firstSel.value === 'yes') {
      const entryDisp = $('#entry_date_display', panel);
      if (!parseDobDisplay(entryDisp.value)) {
        errs.push(errorUnder(entryDisp, 'Enter date as DD | MMM | YYYY.'));
      } else {
        normalizeDateInput(entryDisp);
      }
      const birthCountry = $('#birth_country', panel);
      if (!birthCountry.value.trim()) {
        errs.push(errorUnder(birthCountry, 'Country is required.'));
      }
   if (wiWrapper && wiWrapper.classList.contains('is-hidden') === false) {
  const incomeRows = panel.querySelectorAll('.wi-col--income .wi-row');
  const periodRows = panel.querySelectorAll('.wi-col--period .wi-row');

  ['inc_y1','inc_y2','inc_y3'].forEach((id, idx) => {
    const inp = $('#'+id, panel);
    if (inp && !inp.value.trim()) {
      // normal error on the input
      errs.push(errorUnder(inp, 'Enter amount or 0.'));

      // right column row
      const incomeRow = inp.closest('.wi-row');
      if (incomeRow) incomeRow.classList.add('wi-row-error');

      // left column row with the same index
      const periodRow = periodRows[idx];
      if (periodRow) periodRow.classList.add('wi-row-error');
    }
  });
}


    }

    // 2) moved?
    const movedSel = panel.querySelector('input[name="moved_province"]:checked');
    if (!movedSel) {
      errs.push(errorUnder($('#mprov_yes', panel), 'Please select Yes or No.'));
    } else if (movedSel.value === 'yes') {
      const movedDisp = $('#moved_date_display', panel);
      if (!parseDobDisplay(movedDisp.value)) {
        errs.push(errorUnder(movedDisp, 'Enter date as DD | MMM | YYYY.'));
      } else {
        normalizeDateInput(movedDisp);
      }
      const provFrom = $('#prov_from', panel);
      const provTo   = $('#prov_to', panel);
      if (!provFrom.value.trim()) errs.push(errorUnder(provFrom, 'Select a province.'));
      if (!provTo.value.trim())   errs.push(errorUnder(provTo,   'Select a province.'));

      const movexpSel = panel.querySelector('input[name="moving_expenses_claim"]:checked');
      if (!movexpSel) {
        errs.push(errorUnder($('#movexp_yes', panel), 'Please select Yes or No.'));
      } else if (movexpSel.value === 'yes') {
        const prevAddr = $('#moving_prev_address', panel);
        const dist     = $('#moving_distance', panel);
        if (!prevAddr.value.trim()) errs.push(errorUnder(prevAddr, 'Previous address is required.'));
        if (!dist.value.trim())     errs.push(errorUnder(dist, 'Distance is required.'));
      }
    }

    // 3) FTHB
    const fthbSel = panel.querySelector('input[name="first_home_buyer"]:checked');
    if (!fthbSel) {
      errs.push(errorUnder($('#fthb_yes', panel), 'Please select Yes or No.'));
    } else if (fthbSel.value === 'yes') {
      const purchase = $('#first_home_purchase_display', panel);
      if (!parseDobDisplay(purchase.value)) {
        errs.push(errorUnder(purchase, 'Enter date as DD | MMM | YYYY.'));
      } else {
        normalizeDateInput(purchase);
      }
    }

    // 4) sole owner
    const claimSel = panel.querySelector('input[name="claim_full"]:checked');
    if (!claimSel) {
      errs.push(errorUnder($('#claim_full_yes', panel), 'Please select Yes or No.'));
    } else if (claimSel.value === 'no') {
      const ownerCount = $('#owner_count', panel);
      if (!ownerCount.value.trim()) {
        errs.push(errorUnder(ownerCount, '# of owners is required.'));
      }
    }

    // 5) rent
    const rentSel = panel.querySelector('input[name="onRent"]:checked');
    if (!rentSel) {
      errs.push(errorUnder($('#onrent_yes', panel), 'Please choose Yes or No.'));
    } else if (rentSel.value === 'yes') {
      const claimRentSel = panel.querySelector('input[name="claimRent"]:checked');
      if (!claimRentSel) {
        errs.push(errorUnder($('#claimrent_yes', panel), 'Please choose Yes or No.'));
      } else if (claimRentSel.value === 'yes') {
        // IMPORTANT: ignore display:none, just check the table itself
        const rentWrap = $('#rent-addresses', panel);
        const emptyRow = rentWrap
          ? rentWrap.querySelector('#rent-empty-row, .rent-empty-row')
          : null;
        if (emptyRow) {
          errs.push(errorUnder(rentWrap, 'Please add at least one rent address.'));
        }
      }
    }

    if (errs.length) {
      showBanner();
      const first = errs[0];
      if (first) {
        const focusable = first.querySelector('input,select,textarea,button') || first;
        focusable?.focus?.();
        first.scrollIntoView({behavior:'smooth', block:'center'});
      }
      return false;
    }
    return true;
  }

  /* ----------------------------------------------------------
     LIVE CLEANUP
     ---------------------------------------------------------- */
  panel.addEventListener('input', function(e){
  const host = e.target.closest('.fi-group, .yn-group, .qs-block, #rent-addresses');
  if (host?.classList.contains('is-invalid')) {
    host.classList.remove('is-invalid');
    host.querySelector('.fi-error-text')?.remove();
  }

    if (e.target.id === 'moved_date_display') {
    const g = e.target.closest('.fi-group');
    if (g && g.classList.contains('has-date-error')) {
      g.classList.remove('has-date-error');
    }
  }

  // if fixing world income, remove tall class on BOTH columns
  if (e.target.id === 'inc_y1' || e.target.id === 'inc_y2' || e.target.id === 'inc_y3') {
    // right side
    const incomeRow = e.target.closest('.wi-row');
    if (incomeRow && e.target.value.trim() !== '') {
      incomeRow.classList.remove('wi-row-error');

      // figure out index of this row among income rows
      const incomeRows = Array.from(panel.querySelectorAll('.wi-col--income .wi-row'));
      const idx = incomeRows.indexOf(incomeRow);
      if (idx > -1) {
        const periodRows = panel.querySelectorAll('.wi-col--period .wi-row');
        const periodRow = periodRows[idx];
        if (periodRow) periodRow.classList.remove('wi-row-error');
      }
    }
  }

  if (!panel.querySelector('.is-invalid')) hideBanner();
}, true);


  /* ----------------------------------------------------------
     HOOK CONTINUE
     ---------------------------------------------------------- */
  const nextBtn = panel.querySelector('.continue-btn[data-goto="next"]');
  if (nextBtn) {
    nextBtn.addEventListener('click', function(ev){
      if (!validateTaxPanel()) {
        ev.preventDefault();
        ev.stopPropagation();
        ev.stopImmediatePropagation();
      }
    }, true);
  }

  // init
  toggleFirstTime();
  toggleMoved();
  toggleMovExp();
  toggleFTHB();
  toggleClaimFull();
  toggleRent();
  ensureBanner();
})();
</script>