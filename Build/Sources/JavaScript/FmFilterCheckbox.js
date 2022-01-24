import { html, css, LitElement } from 'lit';

export class FmFilterCheckbox extends LitElement {
  static get properties() {
    return {
      id: { type: String, attribute: 'data-filter-id' },
      items: { type: Array },
    };
  }

  _scaffoldHTML = null;
  _scaffold = null;
  _itemsParent = null;
  _itemTemplate = null;

  constructor() {
    super();
    this._scaffoldHTML = this.innerHTML;
    this.innerHTML = '';
  }

  createRenderRoot() {
    return this;
  }

  getScaffold() {
    if (this._scaffold === null) {
      this._scaffold = document.createRange().createContextualFragment(this._scaffoldHTML);
      let itemTemplate = this._scaffold.querySelector('[data-filter="item-template"]');
      this._itemsParent = itemTemplate.parentNode;
      this._itemTemplate = itemTemplate.cloneNode(true);
      this._scaffold.removeChild(itemTemplate);
    }
    return this._scaffold;
  }

  render() {
    let content = this.getScaffold();
    if (!this.items || this.items.length === 0) {
      return content;
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
    return content;
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
