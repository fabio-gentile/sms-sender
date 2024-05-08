const GSM_7_BIT_CHARS = "@£$¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\\\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà";
const GSM_7_BIT_EX_CHAR = "\\^{}\\\\\\[~\\]|€\\r\\n";
const GSM_7_BIT_REG_EXP = RegExp("^[" + GSM_7_BIT_CHARS + "]*$");
const GSM_7_BIT_EX_REG_EXP = RegExp("^[" + GSM_7_BIT_CHARS + GSM_7_BIT_EX_CHAR + "]*$");
const GSM_7_BIT_EX_ONLY_REG_EXP = RegExp("^[\\" + GSM_7_BIT_EX_CHAR + "]*$");
const GSM_7BIT = 'GSM_7BIT';
const GSM_7BIT_EX = 'GSM_7BIT_EX';
const UTF16 = 'UTF16';

const MESSAGE_LENGTH = {
    GSM_7BIT: 160,
    GSM_7BIT_EX: 160,
    UTF16: 70
};

const MULTI_MESSAGE_LENGTH = {
    GSM_7BIT: 153,
    GSM_7BIT_EX: 153,
    UTF16: 67
};

function count(text) {
    const encoding = detectEncoding(text);
    let length = text.length;

    if (encoding === GSM_7BIT_EX) {
        length += countGsm7bitEx(text);
    }

    let perMessage = MESSAGE_LENGTH[encoding];
    if (length > perMessage) {
        perMessage = MULTI_MESSAGE_LENGTH[encoding];
    }

    const messages = Math.ceil(length / perMessage);
    let remaining = (perMessage * messages) - length;

    if (remaining === 0 && messages === 0) {
        remaining = perMessage;
    }

    return {
        encoding: encoding,
        length: length,
        perMessage: perMessage,
        perMessageMulti: MULTI_MESSAGE_LENGTH[encoding],
        remaining: remaining,
        messages: messages
    }
}

function detectEncoding(text) {
    if (text.match(GSM_7_BIT_REG_EXP)) {
        return GSM_7BIT;
    }

    if (text.match(GSM_7_BIT_EX_REG_EXP)) {
        return GSM_7BIT_EX;
    }

    return UTF16;
}

function countGsm7bitEx(text) {
    const chars = [];

    for (let _i = 0, _len = text.length; _i < _len; _i++) {
        const char2 = text[_i];
        if (char2.match(GSM_7_BIT_EX_ONLY_REG_EXP) !== null) {
            chars.push(char2);
        }
    }

    return chars.length;
}

export default count;