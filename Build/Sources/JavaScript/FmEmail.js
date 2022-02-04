import { html, css, LitElement } from 'lit';

export class FmEmail extends LitElement {

  _form = null;
  _subject = null;
  _message = null;
  _submitButton = null;
  _receiversComponent = null;

  static properties = {
    config: { type: Object, attribute: 'data-fromes' }
  };

  constructor() {
    super();
    const _config = JSON.parse(this.getAttribute('data-fromes'));
    this._form = this.querySelector('form');
    this._subject = this.querySelector('[data-fromes="subject"]');
    this._message = this.querySelector('[data-fromes="message"]');
    this._submitButton = this.querySelector('[data-fromes="submit"]');
    this._receiversComponent = document.getElementById(_config.receiversComponentId);
    this._subject.value = '';
    this._message.value = '';
    this._submitButton.classList.add('disabled');
    this._subject.addEventListener('keyup', this.renderSubmitButton.bind(this));
    this._message.addEventListener('keyup', this.renderSubmitButton.bind(this));
    this._submitButton.addEventListener('click', this.handleFormSubmitEvent.bind(this));
    this._receiversComponent.addEventListener('change', this.renderSubmitButton.bind(this));
  }

  createRenderRoot() {
    return this;
  }

  handleFormSubmitEvent(event) {
    event.stopPropagation();
    if (!this.canSubmit()) {
      return;
    }
    const items = this._receiversComponent.items;
    const itemIds = items.map(item => item.id);
    this._form.querySelector('[data-fromes="receivers"]').value = JSON.stringify(itemIds);
    this.createServerRequest();
  }

  hideStatusMessage() {
    setTimeout((function() {
      this.querySelector('[data-fromes="mail-success"]').classList.add('d-none');
      this.querySelector('[data-fromes="mail-error"]').classList.add('d-none');
    }).bind(this), 5000);
  }

  showSuccessMessage() {
    this.querySelector('[data-fromes="mail-success"]').classList.remove('d-none');
    this.hideStatusMessage();
  }

  showErrorMessage() {
    this.querySelector('[data-fromes="mail-error"]').classList.remove('d-none');
    this.hideStatusMessage();
  }

  createServerRequest() {
    fetch(
      this._form.action,
      {
        method: 'post',
        headers: {
          'Fromes' : this.config['accessToken']
        },
        body: (new FormData(this._form)),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("HTTP error, status = " + response.status);
        }
        return response.json();
      })
      .then(data => {
        this.showSuccessMessage();
        this._receiversComponent.items = [];
        this._subject.value = '';
        this._message.value = '';
        // console.log('Success:', data);
      })
      .catch((error) => {
        this.showErrorMessage();
        console.error('Error on sending email', { code: '1643305297', error: error });
      });
  }

  canSubmit() {
    const subject = this._form.querySelector('[data-fromes="subject"]').value;
    const message = this._form.querySelector('[data-fromes="message"]').value;
    let receivers = this._receiversComponent.items ? this._receiversComponent.items : [];
    return !(subject.length < 3 || message.length < 10 || receivers.length === 0);
  }

  renderSubmitButton()
  {
    this._submitButton.classList.add('disabled');
    if (this.canSubmit()) {
      this._submitButton.classList.remove('disabled');
    }
  }

  render() {
    this.renderSubmitButton();
  }
}

customElements.define('fm-email', FmEmail);
