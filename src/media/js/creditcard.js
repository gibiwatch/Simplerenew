jQuery.Simplerenew = jQuery.extend({}, jQuery.Simplerenew, {
    creditCard: (function () {
        var cards = [
            { name: 'Visa', cvvlen: 3, prefixes: [4] },
            { name: 'MasterCard', cvvlen: 3, prefixes: [51, 52, 53, 54, 55] },
            { name: 'American Express', cvvlen: 4, prefixes: [34, 37] },
            { name: 'Discover', cvvlen: 3, prefixes: [6011, 62, 64, 65] },
            { name: 'Diner\'s Club', cvvlen: 3, prefixes: [305, 36, 38] },
            { name: 'Carte Blanche', cvvlen: 3, prefixes: [300, 301, 302, 303, 304, 305] },
            { name: 'JCB', cvvlen: 3, prefixes: [35] },
            { name: 'enRoute', cvvlen: 3, prefixes: [2014, 2149] },
            { name: 'Solo', cvvlen: 3, prefixes: [6334, 6767] },
            { name: 'Switch', cvvlen: 3, prefixes: [4903, 4905, 4911, 4936, 564182, 633110, 6333, 6759] },
            { name: 'Maestro', cvvlen: 3, prefixes: [5018, 5020, 5038, 6304, 6759, 6761] },
            { name: 'Visa', cvvlen: 3, prefixes: [417500, 4917, 4913, 4508, 4844] }, // Visa Electron
            { name: 'Laser', cvvlen: 3, prefixes: [6304, 6706, 6771, 6709] }
        ];

        var getType = function (ccnumber) {
            var card, prefix;

            for (var c = 0; c < cards.length; c++) {
                card = cards[c];

                for (var p = 0; p < card.prefixes.length; p++) {
                    prefix = card.prefixes[p];
                    if (new RegExp('^' + prefix.toString()).test(ccnumber)) {
                        return card;
                    }
                }
            }
            return null;
        };

        /*
         * The following method was found at The JavaScript Source!!
         * http://www.javascriptsource.com Created by: David Leppek ::
         * https://www.azcode.com/Mod10
         *
         * Determine if a credit card number could be valid before submitting it for
         * real-time online authentication. Based on ANSI X4.13, the LUHN formula,
         * aka the Mod 10 algorithm is used to validate accurate credit card
         * numbers.
         *
         * v2.0 11/7/2005
         */
        var Mod10 = function (rawNumb) {
            var ccNumb = rawNumb.replace(/[\s-]/g, '');
            var valid = "0123456789";               // Valid digits in a credit card number
            var len = ccNumb.length;                // The length of the submitted cc number
            var iCCN = parseInt(ccNumb, 10);        // integer of ccNumb
            var sCCN = ccNumb.toString();           // string of ccNumb
            sCCN = sCCN.replace(/^\s+|\s+$/g, '');  // strip spaces
            var iTotal = 0;                         // integer total set at zero
            var bNum = true;                        // by default assume it is a number
            var bResult = false;                    // by default assume it is NOT a valid cc
            var temp;                               // temp variable for parsing string
            var calc;                               // used for calculation of each digit

            // Determine if the ccNumb is in fact all numbers
            for (var j = 0; j < len; j++) {
                temp = "" + sCCN.substring(j, j + 1);
                if (valid.indexOf(temp) == "-1") {
                    return false;
                }
            }

            // Determine if it is the proper length
            if (len === 0) {
                // nothing, field is blank AND passed above # check
                return false;
            }

            // ccNumb is a number and the proper length - let's see if it is a valid
            // card number
            if (len >= 15) {
                // 15 or 16 for Amex or V/MC
                for (var i = len; i > 0; i--) {
                    calc = parseInt(iCCN, 10) % 10; // right most digit
                    calc = parseInt(calc, 10);      // assure it is an integer
                    iTotal += calc;                 // running total of the card number as we loop - Do Nothing to first digit
                    i--;                            // decrement the count - move to the next digit in the card
                    iCCN = iCCN / 10;               // subtracts right most digit from ccNumb
                    calc = parseInt(iCCN, 10) % 10; // NEXT right most digit
                    calc = calc * 2;                // multiply the digit by two

                    /*
                     * Instead of some screwy method of converting 16 to a string
                     * and then parsing 1 and 6 and then adding them to make 7, I
                     * use a simple switch statement to change the value of calc2 to
                     * 7 if 16 is the multiple.
                     */
                    switch (calc) {
                        case 10:
                            calc = 1;
                            break;       // 5*2=10 & 1+0 = 1
                        case 12:
                            calc = 3;
                            break;       // 6*2=12 & 1+2 = 3
                        case 14:
                            calc = 5;
                            break;       // 7*2=14 & 1+4 = 5
                        case 16:
                            calc = 7;
                            break;       // 8*2=16 & 1+6 = 7
                        case 18:
                            calc = 9;
                            break;       // 9*2=18 & 1+8 = 9
                        default:         // calc = calc; 4*2 = 8 & 8 = 8 -same for all
                        // lower numbers
                    }
                    iCCN = iCCN / 10;  // subtracts right most digit from ccNum
                    iTotal += calc;  // running total of the card number as we loop
                }

                // Could be valid cc# if the sum Mod 10 is zero
                return ((iTotal % 10) === 0);
            }
            return false;
        };

        var verifyCVV = function (rawNumber, cvv) {
            var ccnumber = rawNumber.replace(/[\s-]/g, '');
            var card = getType(ccnumber);
            if (card) {
                return (cvv >= 1 && cvv <= 9999 && cvv.length == card.cvvlen);
            }
            return true;
        };

        var verifyDate = function (ccmonth, ccyear) {
            if (ccmonth > 0 && ccyear > 0) {
                var now = new Date();
                var baseDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                var cardDate = new Date(ccyear, ccmonth, 1);
                return (baseDate < cardDate);
            }
            return true;
        };

        return {
            getType: getType,
            verifyNumber: Mod10,
            verifyCVV: verifyCVV,
            verifyDate: verifyDate
        };
    })()
});
