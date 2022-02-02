import { FmAbstractList } from "./FmAbstractList";

/**
 * List of selectable checkbox items with a control bar to select all or none elements.
 * An item is an object containing at least the property label: `item = { label: 'Label' }`
 * The dom node representing the item just uses the items label and holds a reference to its index in the items array.
 */
export class FmSelectList extends FmAbstractList {
  get selected() {
    let inputs = this.querySelectorAll('input:checked');
    const result = [];
    for (const input of inputs) {
      result.push(this.items[input.value]);
    }
    return result;
  }

  get value() {
    const inputs = this.querySelectorAll('input:checked');
    const result = [];
    for (const input of inputs) {
      result.push(this.items[input.value].id ?? input.value);
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

  isProcessEvent(event) {
    // Select all or none
    if (event.type === 'click') {
      const data = event.target.dataset.fromes;
      if (data === 'select-all' || data === 'select-none') {
        event.stopPropagation();
        const inputs = this._itemsParent.querySelectorAll('input');
        inputs.forEach(function (input) {
          input.checked = data === 'select-all';
        })
        return true;
      }
    }
    // Change one element
    return event.type === 'change' && event.target.type === 'checkbox';
  }
}

customElements.define('fm-select-list', FmSelectList);
