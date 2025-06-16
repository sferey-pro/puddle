import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["timer", "callback", ""]

  static values = {
    interval: { default: 1000, type: Number },
    callbackIdentifier: String,
    callbackFunctionName: String,
    timeLeft: Number,
    prefix: String,
  }

  initialize() {
    this.timerTarget.innerHTML = this.prefixValue + " " + this.timeLeftValue + " seconds"
  }

  connect() {
    this._timer = setInterval(() => {
      this.update()
    }, this.intervalValue)
  }

  timeLeftValueChanged(value) {
    if (this.hasCallbackTarget && value <= 0) {
      const otherController = this.application.getControllerForElementAndIdentifier(
        this.callbackTarget,
        this.callbackIdentifierValue
      )
      eval("otherController." + this.callbackFunctionNameValue + "()")
    }
  }

  update() {
    var timeLeftText = "seconds"
    if (this.timeLeftValue <= 1) {
      timeLeftText = "second"
    }

    this.timeLeftValue -= 1

    if (this.timeLeftValue <= 0) {
      this.disconnect()
      this.timerTarget.remove()
    } else {
      this.timerTarget.innerHTML = this.prefixValue + " " + this.timeLeftValue + " " + timeLeftText
    }
  }

  stopTimer() {
    const timer = this._timer

    if (!timer) return

    clearInterval(timer)
  }

  disconnect() {
    // ensure we clean up so the timer is not running if the element gets removed
    this.stopTimer()
  }
}
