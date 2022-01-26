import { html, css, LitElement } from 'lit';

export class FmFilter extends LitElement {

  _subfilters = [];
  _form = null;

  static properties = {
    config: { type: Object, attribute: 'data-config' }
  };

  constructor() {
    super();
    this._form = this.removeChild(this.getElementsByTagName('form')[0]);
    this.addEventListener('change', this.handleChangeEvent);
  }

  createRenderRoot() {
    return this;
  }

  initializeFilters() {
    for (const config of this.config['jsonFilter']) {
      let subfilter = this.querySelector('#' + config.id);
      if (subfilter !== null) {
        subfilter.items = config.items;
        this._subfilters.push(subfilter);
      }
    }
  }

  render() {
    this.initializeFilters();
  }

  handleChangeEvent(event) {
    const filterStatus = [];
    for (const subfilter of this._subfilters) {
      filterStatus.push({ id: subfilter.id, value: subfilter.value });
    }
    event.stopPropagation();
    this.createServerRequest(filterStatus);
  }

  createServerRequest(data) {
    this.setFormData(data);
    fetch(
      this._form.action,
      {
      method: 'post',
      headers: {
        'Fromes' : this.config['accessToken']
      },
      body: (new FormData(this._form)),
    })
      .then(response => response.json())
      .then(data => {
        console.log('Success:', data);
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }

  setFormData(data) {
    this._form.querySelector('[data-filter="status"]').value = JSON.stringify({ data });
  }
}

customElements.define('fm-filter', FmFilter);
