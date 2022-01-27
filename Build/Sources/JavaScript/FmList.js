import { FmAbstractItems } from "./FmAbstractItems";

/**
 * List of selectable items.
 * An item is an object containing at least the property label: `item = { label: 'Label' }`
 * The dom node representing the item just uses the items label and holds a reference to its index in the items array.
 */
export class FmList extends FmAbstractItems {
  constructor() {
    super();
    this.addEventListener('click', this.handleClickEvent);
  }

  get selected() {
    let inputs = this.querySelectorAll('input:checked');
    const result = [];
    for (const input of inputs) {
      result.push(this.items[input.value]);
    }
    return result;
  }

  getItemNode(item, index) {
    let node = this._itemTemplate.cloneNode(true);
    let input = node.getElementsByTagName('input')[0];
    let label = node.getElementsByTagName('label')[0];
    label.htmlFor = input.name = input.id = `${this.id}-${index}`;
    input.value = index;
    label.textContent = item.label;
    return node;
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
