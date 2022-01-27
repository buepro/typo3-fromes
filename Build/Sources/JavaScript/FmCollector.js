import { FmAbstractItems } from "./FmAbstractItems";

/**
 * Collects items where an item is an object.
 * In case the items contain an id property it will be used to ensure an element is just collected once.
 */
export class FmCollector extends FmAbstractItems {
  constructor() {
    super();
    this.addEventListener('click', this.handleClickEvent);
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
    let closeNode = this.querySelector('[data-filter="close"]');
    closeNode.classList.add('invisible');
    if (this.items && this.items.length > 0) {
      closeNode.classList.remove('invisible');
    }
    FmAbstractItems.prototype.render.call(this);
  }

  handleClickEvent(event) {
    if (event.target.dataset.filter === 'close') {
      this.items = [];
    }
    event.stopPropagation();
  }
}

customElements.define('fm-collector', FmCollector);
