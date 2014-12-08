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

        /**
         * Nicely elegant luhn test! Thanks to:
         * https://gist.github.com/DiegoSalazar/4075533
         *
         * Determine if a credit card number could be valid before submitting it for
         * real-time online authentication. Based on ANSI X4.13, the LUHN formula,
         * aka the Mod 10 algorithm is used to validate accurate credit card
         * numbers.
         */
        var luhnTest = function (value) {
            // accept only digits, dashes or spaces
            if (/[^0-9-\s]+/.test(value)) return false;

            // The Luhn Algorithm. It's so pretty.
            var nCheck = 0, cDigit, nDigit = 0, bEven = false;
            value = value.replace(/\D/g, "");

            for (var n = value.length - 1; n >= 0; n--) {
                cDigit = value.charAt(n);
                nDigit = parseInt(cDigit, 10);

                if (bEven) {
                    if ((nDigit *= 2) > 9) nDigit -= 9;
                }

                nCheck += nDigit;
                bEven = !bEven;
            }

            return (nCheck % 10) == 0;
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
            verifyNumber: luhnTest,
            verifyCVV: verifyCVV,
            verifyDate: verifyDate
        };
    })()
});
