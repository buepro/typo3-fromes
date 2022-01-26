import { html, css, LitElement } from 'lit';

export class FmList extends LitElement {
  static get properties() {
    return {
      id: { type: String },
      items: { type: Array },
    };
  }

  _itemsParent = null;
  _itemTemplate = null;

  constructor() {
    super();
    let itemTemplate = this.querySelector('[data-filter="item-template"]');
    this._itemsParent = itemTemplate.parentNode;
    this._itemTemplate = itemTemplate.cloneNode(true);
    itemTemplate.remove();
    this.addEventListener('click', this.handleClickEvent);
  }

  createRenderRoot() {
    return this;
  }

  render() {
    this._itemsParent.textContent = '';
    if (!this.items || this.items.length === 0) {
      return;
    }
    let counter = 0;
    for (const item of this.items) {
      counter++;
      let node = this._itemTemplate.cloneNode(true);
      let input = node.getElementsByTagName('input')[0];
      let label = node.getElementsByTagName('label')[0];
      label.htmlFor = input.name = input.id = `${this.id}-${counter}`;
      input.value = JSON.stringify(item);
      label.textContent = item.name ? item.name : item.email;
      this._itemsParent.appendChild(node);
    }
  }

  handleClickEvent(event) {
    const data = event.target.dataset.filter;
    if (data === 'select-all' || data === 'select-none') {
      const inputs = this._itemsParent.querySelectorAll('input');
      inputs.forEach(function (input) {
        input.checked = data === 'select-all';
      })
    }
    event.stopPropagation();
  }
}

customElements.define('fm-list', FmList);
