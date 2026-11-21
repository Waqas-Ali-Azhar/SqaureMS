var style = document.createElement('style');
var lastOpened = [];
let selectionChanged = false; //--
style.setAttribute("id", "multiselect_dropdown_styles");
style.innerHTML = `
.multiselect-dropdown{
  min-width:120px !important;
  display: inline-flex;
  align-items: center;
  height:22px;
  border-radius: 4px;
  border: none;
  background-color: #f1f1f1;
  position: relative;
  background-image: url('gfx/input-arrow.svg');
  background-repeat: no-repeat;
  background-position: right .3rem center;
  background-size: 16px 12px;
  margin: 4px;
}
.multiselect-dropdown span.optext, .multiselect-dropdown span.placeholder{
  margin-right:0.5em; 
  margin-left: 4px;
  padding:1px 0; 
  border-radius: 4px; 
  display: inline-flex;
  align-items: center;
}
.multiselect-dropdown span.optext{
  background-color:lightgray;
  padding:1px 0.75em; 
}
.multiselect-dropdown span.optext .optdel {
  float: right;
  margin: 0 -4px 0px 5px;
  font-size: 0.7em;
  cursor: pointer;
  content: '';
  background-image: url('gfx/close-btn.svg');
  margin-left: 6px;
  font-size: 10px;
  background-size: 8px 8px;
  width: 8px;
  height: 8px;
}
.multiselect-dropdown span.optext .optdel:hover { filter: invert(46%) sepia(64%) saturate(427%) hue-rotate(313deg) brightness(93%) contrast(87%);}
.multiselect-dropdown span.placeholder{
  color:#000;
  background-color:white;
  border:1px solid;
  width:inherit;
  cursor:pointer;
}
.multiselect-dropdown-list-wrapper{
  box-shadow: gray 0 3px 8px;
  z-index: 100;
  padding:2px;
  border-radius: 4px;
  border: solid 1px #ced4da;
  display: none;
  margin: -1px;
  position: absolute;
  top:0;
  left: 0;
  right: 0;
  background: white;
  width: calc(100% + 60px);
}
.multiselect-dropdown-list-wrapper .multiselect-dropdown-search{
  margin-bottom:5px;
}
.multiselect-dropdown-list{
  padding:2px;
  height: 15rem;
  overflow-y:auto;
  overflow-x: hidden;
}
.multiselect-dropdown-list::-webkit-scrollbar {
  width: 6px;
}
.multiselect-dropdown-list::-webkit-scrollbar-thumb {
  background-color: #bec4ca;
  border-radius:3px;
}

.multiselect-dropdown-list div{
  padding: 2px 0;
  display: flex;
    align-items: center;
    gap: 2px;
}
.multiselect-dropdown-list input{
  height: 1.15em;
  width: 1.15em;
  margin-right: 0.35em;  
}
.multiselect-dropdown-list div.checked{
}
.multiselect-dropdown-list div:hover{
  background-color: #ced4da;
}
.multiselect-dropdown-all-selector {border-bottom:solid 1px #999;}
`;
document.head.appendChild(style);

function MultiselectDropdown(options) {
  var config = {
    search: true,
    height: '15rem',
    style: {
      width: '90%',
    },
    placeholder: 'Select',
    txtSelected: 'selected',
    txtAll: 'All',
    txtRemove: 'Remove',
    txtSearch: 'Search',
    ...options
  };
  function newEl(tag, attrs) {
    var e = document.createElement(tag);
    if (attrs !== undefined) Object.keys(attrs).forEach(k => {
      if (k === 'class') { Array.isArray(attrs[k]) ? attrs[k].forEach(o => o !== '' ? e.classList.add(o) : 0) : (attrs[k] !== '' ? e.classList.add(attrs[k]) : 0) }
      else if (k === 'style') {
        Object.keys(attrs[k]).forEach(ks => {
          e.style[ks] = attrs[k][ks];
        });
      }
      else if (k === 'text') { attrs[k] === '' ? e.innerHTML = '&nbsp;' : e.innerText = attrs[k] }
      else {
        if (k.startsWith('data-')) e.setAttribute(k, attrs[k]);
        else e[k] = attrs[k];
      }
    });
    return e;
  }

  document.querySelectorAll("select[multiple]").forEach((el, k) => {
    var divId = el.id + '_ml';
    var div = newEl('div', {
      class: 'multiselect-dropdown',
      id: divId,
      style: {
        width: config.style?.width ?? el.clientWidth + 'px',
        padding: config.style?.padding ?? ''
      }
    });
    el.style.display = 'none';
    el.parentNode.insertBefore(div, el.nextSibling);
    var listWrap = newEl('div', { class: 'multiselect-dropdown-list-wrapper' });
    var list = newEl('div', { class: 'multiselect-dropdown-list', style: { height: config.height } });
    var submitBtn = newEl('button', { class: 'mainbtn', style: { width: '100%' } });
    var search = newEl('input', { class: ['multiselect-dropdown-search'].concat([config.searchInput?.class ?? 'form-control']), style: { width: '100%', display: el.attributes['multiselect-search']?.value === 'true' ? 'block' : 'none' }, placeholder: config.txtSearch });
    listWrap.appendChild(search);
    div.appendChild(listWrap);
    listWrap.appendChild(list);
    listWrap.appendChild(submitBtn);

    el.loadOptions = () => {
      list.innerHTML = '';
      submitBtn.innerHTML = 'Search';
      submitBtn.setAttribute("onclick", "filterBy();");

      if (el.attributes['multiselect-select-all']?.value == 'true') {
        var op = newEl('div', { class: 'multiselect-dropdown-all-selector' })
        var ic = newEl('input', { type: 'checkbox' });
        op.appendChild(ic);
        op.appendChild(newEl('label', { text: config.txtAll }));

        op.addEventListener('click', () => {
          op.classList.toggle('checked');
          op.querySelector("input").checked = !op.querySelector("input").checked;

          var ch = op.querySelector("input").checked;
          list.querySelectorAll(":scope > div:not(.multiselect-dropdown-all-selector)")
            .forEach(i => { if (i.style.display !== 'none') { i.querySelector("input").checked = ch; i.optEl.selected = ch } });

          el.dispatchEvent(new Event('change'));
          selectionChanged = true; 
        });
        ic.addEventListener('click', (ev) => {
          ic.checked = !ic.checked;
        });
        list.appendChild(op);
      }

      Array.from(el.options).map(o => {
        var op = newEl('div', { class: o.selected ? 'checked' : '', optEl: o })
        var ic = newEl('input', { type: 'checkbox', checked: o.selected });
        op.appendChild(ic);
        if (['ct_campaign_text_ee', 'ct_campaign_text_lv', 'ct_campaign_text_lt', 'ct_campaign_text_fi', 'c_campaign_text_ee', 'c_campaign_text_lv', 'c_campaign_text_lt', 'c_campaign_text_fi'].includes(el.id)) {
          op.appendChild(newEl('label', { innerHTML: o.innerHTML }));
        } else {
          op.appendChild(newEl('label', { text: o.text }));
        }
        op.addEventListener('click', () => {
          op.classList.toggle('checked');
          op.querySelector("input").checked = !op.querySelector("input").checked;
          op.optEl.selected = !!!op.optEl.selected;
          el.dispatchEvent(new Event('change'));
          selectionChanged = true;
        });
        ic.addEventListener('click', (ev) => {
          ic.checked = !ic.checked;
        });
        o.listitemEl = op;
        list.appendChild(op);
      });
      div.listEl = listWrap;

      div.refresh = () => {
        div.querySelectorAll('span.optext, span.placeholder').forEach(t => div.removeChild(t));
        var sels = Array.from(el.selectedOptions);
        if (sels.length > (el.attributes['multiselect-max-items']?.value ?? 5)) {
          var cm = newEl('span', { class: 'optext', text: sels.length + ' ' + config.txtSelected });
          elid = $(el).attr('id');
          cm.appendChild(newEl('span', { id: 'x_' + elid, class: 'optdel', title: config.txtRemove, onclick: (ev) => { div.refresh(); ev.stopPropagation(); resetMultiselect(ev.target.id.slice(2)); filterBy(); } }));
          div.appendChild(cm);
          bindMyEvents();
        }
        else {
          sels.map(x => {
            var c = newEl('span', { class: 'optext', text: x.text, srcOption: x });
            if ((el.attributes['multiselect-hide-x']?.value !== 'true'))
              c.appendChild(newEl('span', { class: 'optdel', title: config.txtRemove, onclick: (ev) => { c.srcOption.listitemEl.dispatchEvent(new Event('click')); div.refresh(); ev.stopPropagation(); filterBy(); } }));

            div.appendChild(c);
          });
        }
        if (0 == el.selectedOptions.length) {
          div.appendChild(newEl('span', {
            class: 'placeholder',
            text: el.attributes['placeholder']?.value ?? config.placeholder,
            'data-parent-id': div.id
          }));
        }
        document.querySelectorAll('.mw_tooltip').forEach(function (elem) {
          elem.style.position = 'relative';
        });
        const tooltips = document.querySelectorAll('.mw_tooltip');
        if (tooltips.length > 0) {
          tooltips.forEach(function (elem) {
            elem.style.position = 'relative';
          });
        }

      };
      div.refresh();
    }
    el.loadOptions();

    search.addEventListener('input', () => {
      list.querySelectorAll(":scope div:not(.multiselect-dropdown-all-selector)").forEach(d => {
        var txt = d.querySelector("label").innerText.toUpperCase();
        d.style.display = txt.includes(search.value.toUpperCase()) ? 'block' : 'none';
      });
    });

    div.addEventListener('click', () => {
      lastOpened.push(div.attributes.id.value);
      if (div.previousElementSibling.children.length < 1) {
        el.innerHTML = optionsArr[el.attributes['id'].value];
        el.loadOptions();
      }

      div.listEl.style.display = 'block';
      search.focus();
      search.select();

    });
    //----------------------------------------
    // document.addEventListener('click', function (event) {
    //   if(lastOpened.length > 1){
    //     if(event.target.classList[0]  == 'multiselect-dropdown'){
    //       closeDropdownsByIds(event.target.attributes['id'].value);
    //     }
    //     else if(event.target.classList[0]  == 'placeholder'){
    //       closeDropdownsByIds(event.target.attributes['data-parent-id'].value);
    //     }
    //   }
    //   if (!div.contains(event.target) && event.target.classList[0] != 'placeholder' ) {
    //     listWrap.style.display = 'none';
    //     div.refresh();
    //   }
    // });
    document.addEventListener('click', function (event) {
      if (lastOpened.length > 1) {
        if (event.target.classList[0] == 'multiselect-dropdown') {
          closeDropdownsByIds(event.target.attributes['id'].value);
        }
        else if (event.target.classList[0] == 'placeholder') {
          closeDropdownsByIds(event.target.attributes['data-parent-id'].value);
        }
      }

      // if (!div.contains(event.target) && event.target.classList[0] != 'placeholder') {
      //   listWrap.style.display = 'none';
      //   div.refresh();

      //   //  Submit after closing
      //   const selectElement = el;
      //   if (selectElement.classList.contains('assign-developer')) {
      //     submitDevelopers();
      //   } else if (selectElement.classList.contains('assign-bm')) {
      //     submitBusinessManagers();
      //   }
      // }
      if (!div.contains(event.target) && event.target.classList[0] != 'placeholder') {
         if (listWrap.contains(event.target)) return;
        listWrap.style.display = 'none';
        div.refresh();

        // const selectElement = el;
        // if (selectElement.classList.contains('assign-developer')) {
        //   submitDevelopers(selectElement); // ✅ only for dev
        // } else if (selectElement.classList.contains('assign-bm')) {
        //   submitBusinessManagers(selectElement); // ✅ only for bm
        // }

        //  Wait a little for AJAX calls to finish, then reload
        if (selectionChanged) {
        setTimeout(() => {
          location.reload();
        }, 2000); // Adjust timing if needed
        selectionChanged = false;
      }
    }



    });
    //-------------------------------
  });
}

window.addEventListener('load', () => {
  MultiselectDropdown(window.MultiselectDropdownOptions);
});

function closeDropdownsByIds(current) {

  if (Array.isArray(lastOpened) && lastOpened.length > 1) {
    lastOpened.forEach(id => {
      const parentDiv = document.getElementById(id);
      if (parentDiv && parentDiv.listEl && id != current) {
        parentDiv.listEl.style.display = 'none';
        parentDiv.refresh();
      }
    });
    lastOpened = [];
    lastOpened.push(current)

  }
}

function resetMultiselect(id) {
  $("#" + id + " option").prop("selected", false);
}