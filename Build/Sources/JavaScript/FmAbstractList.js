import { html, css, LitElement } from 'lit';

export class FmAbstractList extends LitElement {
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
    let itemTemplate = this.querySelector('[data-fromes="item-template"]');
    itemTemplate.removeAttribute('data-fromes');
    this._itemsParent = itemTemplate.parentNode;
    this._itemTemplate = itemTemplate.cloneNode(true);
    itemTemplate.remove();
    this.addEventListener('change', this.handleEvent.bind(this));
    this.addEventListener('click', this.handleEvent.bind(this));
  }

  createRenderRoot() {
    return this;
  }

  isProcessEvent(event) {
    return false;
  }

  fireChangeEvent() {
    const changeEvent = new Event('fromes-process-change', { bubbles: true, composed: true });
    this.dispatchEvent(changeEvent);
  }

  handleEvent(event) {
    event.stopPropagation();
    if (this.isProcessEvent(event)) {
      this.fireChangeEvent();
    }
  }

  addItems(newItems) {
    const items = this.items ?? [];
    newItems.forEach(function (newItem) {
      if (newItem.id) {
        let found = items.find(item => newItem.id === item.id)
        if (found) {
          return;
        }
      }
      items.push(newItem);
    });
    this.items = items;
    this.render();
  }

  getItemNode(item, index) {
    // return DOM node
  }

  render() {
    this._notify();
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

  async _notify() {
    await this.updateComplete;
    const event = new Event('change', {bubbles: true, composed: true});
    this.dispatchEvent(event);
  }
}
