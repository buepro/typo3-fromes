import { html, css, LitElement } from 'lit';

export class FmFilter extends LitElement {

  _subfilters = [];
  _form = null;

  static properties = {
    config: { type: Object, attribute: 'data-config' },
    resultComponentId: { type: String, attribute: 'result-id' }
  };

  constructor() {
    super();
    this._form = this.removeChild(this.getElementsByTagName('form')[0]);
    this.addEventListener('fromes-process-change', this.handleChangeEvent);
  }

  createRenderRoot() {
    return this;
  }

  initializeFilters() {
    for (const config of this.config['subfilters']) {
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

  get status() {
    const filterStatus = [];
    for (const subfilter of this._subfilters) {
      filterStatus.push({ id: subfilter.id, value: subfilter.value });
    }
    return filterStatus;
  }

  handleChangeEvent(event) {
    event.stopPropagation();
    this.createServerRequest();
  }

  createServerRequest() {
    this.setFormData(this.status);
    fetch(
      this._form.action,
      {
      method: 'post',
      headers: {
        'Fromes' : 'Filter'
      },
      body: (new FormData(this._form)),
    })
      .then(response => response.json())
      .then(data => {
        document.getElementById(this.resultComponentId).items = data;
      })
      .catch((error) => {
        console.error('Error on receiving filter response', { code: '1643214279', error: error });
      });
  }

  setFormData(data) {
    this._form.querySelector('[data-fromes="status"]').value = JSON.stringify({ data });
  }
}

customElements.define('fm-filter', FmFilter);
