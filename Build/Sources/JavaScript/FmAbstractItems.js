import { html, css, LitElement } from 'lit';

export class FmAbstractItems extends LitElement {
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
    itemTemplate.removeAttribute('data-filter');
    this._itemsParent = itemTemplate.parentNode;
    this._itemTemplate = itemTemplate.cloneNode(true);
    itemTemplate.remove();
  }

  createRenderRoot() {
    return this;
  }

  getItemNode(item, index) {
    // return DOM node
  }

  render() {
    this._itemsParent.textContent = '';
    if (!this.items || this.items.length === 0) {
      return;
    }
    this.items.forEach((function (item, index) {
      let node = this.getItemNode(item, index);
      if (node instanceof Element) {
        this._itemsParent.appendChild(node);
      }
    }).bind(this));
  }
}
