# CPR
Checking danish CPR-numbers

[Personal identification number (Denmark)](https://en.wikipedia.org/wiki/Personal_identification_number_(Denmark))


"The Danish Personal Identification number (Danish: CPR-nummer or personnummer) is a national identification number, which is part of the personal information stored in the Civil Registration System (Danish: Det Centrale Personregister).

The register was established in 1968 by combining information from all the municipal civil registers of Denmark into one.[1]

It is a ten-digit number with the format DDMMYY-SSSS, where DDMMYY is the date of birth and SSSS is a sequence number. The first digit of the sequence number encodes the century of birth (so that centenarians are distinguished from infants), and the last digit of the sequence number is odd for males and even for females.[2]"
[Wikipedia: Personal identification number (Denmark)](https://en.wikipedia.org/wiki/Personal_identification_number_(Denmark))


Dating back to the late sixties this system has several hacks to implement three centuries in a two digit year format (sic!)
This can only be achieved by setting a fixed range of birth years from 1858 to 2057.


## Syntax

Valid syntax 10 digits and one optional '-' at pos 7. i.e. "YYMMDD-SSSm" or "YYMMDDSSSm"

## Gender

Check digit at position 10:
- Odd digit:  Male
- Even digit: Female

## Valid range of years

CPR can only be used for person born between 1858 and 2057

### Ranges to add century to short year

|Day        |Month      |Year       |Serial number  |Range|
|---|---|---|---|---|
|pos. 1-2   |pos. 3–4   |pos. 5-6   |pos. 7-10      |Range      |
|01 - 31    |01 - 12    |00 - 99    |0001 - 0999    |1900 - 1999|
|01 - 31    |01 - 12    |00 - 99    |1000 - 1999    |1900 - 1999|
|01 - 31    |01 - 12    |00 - 99    |2000 - 2999    |1900 - 1999|
|01 - 31    |01 - 12    |00 - 99    |3000 - 3999    |1900 - 1999|
|01 - 31    |01 - 12    |00 - 36    |4000 - 4999    |2000 - 2036|
|01 - 31    |01 - 12    |37 - 99    |4000 - 4999    |1937 - 1999|
|01 - 31    |01 - 12    |00 - 57    |5000 - 5999    |2000 - 2057|
|01 - 31    |01 - 12    |58 - 99    |5000 - 5999    |1858 - 1899|
|01 - 31    |01 - 12    |00 - 57    |6000 - 6999    |2000 - 2057|
|01 - 31    |01 - 12    |58 - 99    |6000 - 6999    |1858 - 1899|
|01 - 31    |01 - 12    |00 - 57    |7000 - 7999    |2000 - 2057|
|01 - 31    |01 - 12    |58 - 99    |7000 - 7999    |1858 - 1899|
|01 - 31    |01 - 12    |00 - 57    |8000 - 8999    |2000 - 2057|
|01 - 31    |01 - 12    |58 - 99    |8000 - 8999    |1858 - 1899|
|01 - 31    |01 - 12    |00 - 36    |9000 - 9999    |2000 - 2036|
|01 - 31    |01 - 12    |37 - 99    |9000 - 9999    |1937 - 1999|



## Dates without Modulus 11

    '010160',	// January 1st 1960
    '010164',	// January 1st 1964
    '010165',	// January 1st 1965
    '010166',	// January 1st 1966
    '010169',	// January 1st 1969
    '010170',	// January 1st 1970
    '010174',	// January 1st 1974
    '010180',	// January 1st 1980
    '010182',	// January 1st 1982
    '010184',	// January 1st 1984
    '010185',	// January 1st 1985
    '010186',	// January 1st 1986
    '010187',	// January 1st 1987
    '010188',	// January 1st 1988
    '010189',	// January 1st 1989
    '010190',	// January 1st 1990
    '010191',	// January 1st 1991
    '010192',	// January 1st 1992

## Demo

[Demo](https://clicketyclick.github.io/cpr/js/)
    
## References

- [Constructing the CPR (Opbygning af CPR-nummeret)](https://www.cpr.dk/media/17534/personnummeret-i-cpr.pdf)
- [CPR wo. Modulus 11](https://cpr.dk/cpr-systemet/personnumre-uden-kontrolciffer-modulus-11-kontrol/)
