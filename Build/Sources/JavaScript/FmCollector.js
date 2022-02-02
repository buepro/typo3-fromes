import { FmAbstractItems } from "./FmAbstractItems";

/**
 * Collects items where an item is an object.
 * In case the items contain an id property it will be used to ensure an element is just collected once.
 */
export class FmCollector extends FmAbstractItems {
  constructor() {
    super();
    this.querySelector('[data-filter="clear"]').addEventListener('click', this.handleClearClickEvent.bind(this));
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

  getItemNode(item, counter) {
    let node = this._itemTemplate.cloneNode(true);
    node.querySelector('[data-filter="label"]').textContent = item.label;
    return node;
  }

  render() {
    let clearNode = this.querySelector('[data-filter="clear"]');
    clearNode.classList.add('invisible');
    if (this.items && this.items.length > 0) {
      clearNode.classList.remove('invisible');
    }
    FmAbstractItems.prototype.render.call(this);
  }

  handleClearClickEvent(event) {
    this.items = [];
    event.stopPropagation();
  }
}

customElements.define('fm-collector', FmCollector);
