import { html, css, LitElement } from 'lit';

export class FmFilterCheckbox extends LitElement {
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
  }

  createRenderRoot() {
    return this;
  }

  render() {
    this._itemsParent.textContent = '';
    if (!this.items || this.items.length === 0) {
      return;
    }
    for (const item of this.items) {
      let node = this._itemTemplate.cloneNode(true);
      let input = node.getElementsByTagName('input')[0];
      let label = node.getElementsByTagName('label')[0];
      label.htmlFor = input.name = input.id = `${this.id}-${item.id}`;
      input.value = item.id;
      if (item.label !== undefined) {
        label.textContent = item.label;
      }
      this._itemsParent.appendChild(node);
    }
  }

  get value () {
    const inputs = this.getElementsByTagName('input');
    const result = [];
    for (const input of inputs) {
      if (input.checked) {
        result.push(input.value);
      }
    }
    return result;
  }
}

customElements.define('fm-filter-checkbox', FmFilterCheckbox);
