/**
 * Class to calculate bowling scores from raw data.
 *
 * @author S.Monk
 * @version 24-Nov-2023
 */
import * as Constants from "./BowlingConstants.js";

class BowlingGame {
  #balls;
  #frames;
  #frameScores;
  #cumulativeScores;
  #totalScore;

  /**
   * Creates a new instance or throws an exception if input is invalid.
   * Input is expected as a single string with balls separated by spaces.
   * Example: "3 4 5 / 6 1 X 2 / X X 0 3 0 / X 2 5"
   *
   * @param {string} raw - the set of ball scores in this game
   */
  constructor(raw) {
    try {
      this.#balls = raw.trim().split(Constants.BALL_SEPARATOR_CHAR);
      this.#frames = buildFrames(this.#balls);
      this.#frameScores = calcFrameScores(this.#frames);
      this.#cumulativeScores = calcCumulativeFrameScores(this.#frameScores);
      this.#totalScore = this.#cumulativeScores[9];
    } catch (err) {
      this.#balls = null;
      this.#frames = null;
      this.#frameScores = null;
      this.#cumulativeScores = null;
      this.#totalScore = null;
      throw new Error(err.message);
    }
  }

  // Public Functions

  get balls() {
    return this.#balls;
  }

  get frames() {
    return this.#frames;
  }

  get frameScores() {
    return this.#frameScores;
  }

  get cumulativeScores() {
    return this.#cumulativeScores;
  }

  get totalScore() {
    return this.#totalScore;
  }

  formatFramesAsTable() {
    let res = "<table>";
    res += "<tr>";
    for (let i = 0; i < this.#frames.length; i++) {
      if (i < 10) {
        res += "<th>" + (i + 1) + "</th>";
      } else {
        res += "<th>Bonus</th>";
      }
    }
    res += "</tr>";

    res += "<tr>";
    for (let i = 0; i < this.#frames.length; i++) {
      res += "<td>" + this.#frames[i] + "</td>";
    }
    res += "</tr>";

    res += "</table>";

    return res;
  }

  formatFrameScoresAsTable() {
    let res = "<table>";
    res += "<tr>";
    for (let i = 0; i < this.#frameScores.length; i++) {
      res += "<th>" + (i + 1) + "</th>";
    }
    res += "</tr>";

    res += "<tr>";
    for (let i = 0; i < this.#frameScores.length; i++) {
      res += "<td>" + this.#frameScores[i] + "</td>";
    }
    res += "</tr>";

    res += "</table>";

    return res;
  }

  formatCumulativeScoresAsTable() {
    let res = "<table>";
    res += "<tr>";

    for (let i = 0; i < this.#cumulativeScores.length; i++) {
      res += "<th>" + (i + 1) + "</th>";
    }
    res += "</tr>";

    res += "<tr>";
    for (let i = 0; i < this.#cumulativeScores.length; i++) {
      res += "<td>" + this.#cumulativeScores[i] + "</td>";
    }
    res += "</tr>";

    res += "</table>";

    return res;
  }
} // end class

// HELPER FUNCTIONS

function buildFrames(balls) {
  if (hasIllegalCharacter(balls)) {
    throw new Error("illegal character in input");
  }

  let frames = [];
  for (let i = 0; i < balls.length; i++) {
    let tempBall = balls[i];
    if (tempBall === Constants.STRIKE_CHAR || i === balls.length - 1) {
      frames.push(tempBall);
    } else {
      frames.push(tempBall + Constants.BALL_SEPARATOR_CHAR + balls[i + 1]);
      i++;
    }
  }

  let msg = checkFrames(frames);
  if (msg !== null) {
    balls = null;
    frames = null;
    throw new Error(msg);
  }

  return frames;
}

function hasIllegalCharacter(arr) {
  let res = false;
  for (let i = 0; i < arr.length; i++) {
    let ch = arr[i];
    if (ch.length !== 1 || !Constants.BOWLING_CHARS.includes(ch)) {
      res = true;
      break;
    }
  }
  return res;
}

function checkFrames(frames) {
  let msg = null;

  if (frames.length < 10) {
    msg = "not enough frames";
    return msg;
  }

  for (let i = 0; i < 10; i++) {
    let temp = frames[i];
    if (!isOpenFrame(temp) && !isSpare(temp) && !isStrike(temp)) {
      msg = "invalid frame [" + temp + "]";
      return msg;
    }
  }

  if (frames.length === 11) {
    let bonusFrame = frames[10];
    if (bonusFrame.length === 1) {
      let ch = bonusFrame[0];
      if (ch !== Constants.STRIKE_CHAR && !Constants.DIGITS.includes(ch)) {
        msg = "invalid frame [" + bonusFrame + "]";
        return msg;
      }
    } else if (!isOpenFrame(bonusFrame) && !isSpare(bonusFrame)) {
      msg = "invalid frame [" + bonusFrame + "]";
      return msg;
    }
  }

  let tenthFrame = frames[9];

  if (isOpenFrame(tenthFrame)) {
    if (frames.length > 10) {
      msg = "too many frames";
    }
  } else if (frames.length === 10) {
    msg = "not enough frames - missing bonus balls";
  } else if (isSpare(tenthFrame)) {
    if (frames.length > 11) {
      msg = "too many bonus frames";
    } else if (frames[10].length !== 1) {
      msg = "too many balls in bonus frame";
    }
  } else if (isStrike(tenthFrame)) {
    if (frames.length > 12) {
      msg = "too many bonus balls";
    } else if (frames.length === 11 && frames[10].length === 1) {
      msg = "not enough bonus balls";
    } else if (frames.length === 12) {
      if (!isStrike(frames[10])) {
        msg = "too many bonus balls";
      } else if (frames[11].length !== 1) {
        msg = "too many bonus balls";
      }
    }
  }
  return msg;
}

function isOpenFrame(str) {
  return (
    str.length === 3 &&
    Constants.DIGITS.includes(str[0]) &&
    str[1] === Constants.BALL_SEPARATOR_CHAR &&
    Constants.DIGITS.includes(str[2]) &&
    Number(str[0]) + Number(str[2]) < Constants.NUM_PINS
  );
}

function isSpare(str) {
  return (
    str.length === 3 &&
    Constants.DIGITS.includes(str[0]) &&
    str[1] === Constants.BALL_SEPARATOR_CHAR &&
    str[2] === Constants.SPARE_CHAR
  );
}

function isStrike(str) {
  return str.length === 1 && str[0] === Constants.STRIKE_CHAR;
}

function calcFrameScores(frames) {
  let frameScores = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
  let score;
  for (let i = 0; i < Constants.NUM_FRAMES; i++) {
    let frame = frames[i];
    if (isOpenFrame(frame)) {
      score = evaluateOpenFrame(frame);
    } else if (isSpare(frame)) {
      let nextBall = frames[i + 1].split(Constants.BALL_SEPARATOR_CHAR)[0];
      score = Constants.NUM_PINS + evaluateBall(nextBall);
    } else if (isStrike(frame)) {
      let nextFrame = frames[i + 1];
      if (isOpenFrame(nextFrame)) {
        score = Constants.NUM_PINS + evaluateOpenFrame(nextFrame);
      } else if (isSpare(nextFrame)) {
        score = Constants.NUM_PINS + Constants.NUM_PINS;
      } else if (isStrike(nextFrame)) {
        let secondBonusBall = frames[i + 2][0]; // can only contain a digit or an 'X'
        score =
          Constants.NUM_PINS +
          Constants.NUM_PINS +
          evaluateBall(secondBonusBall);
      }
    }
    frameScores[i] = score;
  }

  return frameScores;
}

function evaluateOpenFrame(frame) {
  let balls = frame.split(Constants.BALL_SEPARATOR_CHAR);
  let score = evaluateBall(balls[0]) + evaluateBall(balls[1]);
  return score;
}

function calcCumulativeFrameScores(frameScores) {
  let res = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
  res[0] = frameScores[0];
  for (let i = 1; i < res.length; i++) {
    res[i] = res[i - 1] + frameScores[i];
  }

  return res;
}

function evaluateBall(ball) {
  if (ball === "X") {
    return Constants.NUM_PINS;
  } else {
    return Number(ball);
  }
}

export { BowlingGame };
