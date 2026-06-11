// Czech pension retirement age calculation — JS port of RetirementAge.php
// Source: zákon 155/1995 Sb. §32 + příloha, MPSV tables

const MALE_TABLE = {
    1936: [60, 0], 1937: [60, 6], 1938: [61, 0], 1939: [61, 6],
    1940: [62, 0], 1941: [62, 6], 1942: [63, 0], 1943: [63, 6],
    1944: [64, 0], 1945: [64, 0], 1946: [64, 2], 1947: [64, 4],
    1948: [64, 6], 1949: [64, 8], 1950: [64, 10], 1951: [65, 0],
    1952: [65, 0], 1953: [65, 0], 1954: [65, 0], 1955: [65, 0],
    1956: [65, 2], 1957: [65, 4], 1958: [65, 6], 1959: [65, 8],
    1960: [65, 10], 1961: [66, 0], 1962: [66, 2], 1963: [66, 4],
    1964: [66, 6], 1965: [64, 8], 1966: [65, 0], 1967: [65, 2],
    1968: [65, 4], 1969: [65, 6], 1970: [65, 8], 1971: [65, 10],
    1972: [66, 0], 1973: [65, 8],
}

const FEMALE_TABLE_0 = {
    1936: [53, 0], 1937: [53, 4], 1938: [53, 8], 1939: [54, 0],
    1940: [54, 4], 1941: [54, 8], 1942: [55, 0], 1943: [55, 4],
    1944: [55, 8], 1945: [56, 0], 1946: [56, 4], 1947: [56, 8],
    1948: [57, 0], 1949: [57, 4], 1950: [57, 8], 1951: [58, 0],
    1952: [58, 4], 1953: [58, 8], 1954: [59, 0], 1955: [59, 4],
    1956: [59, 8], 1957: [60, 0], 1958: [60, 4], 1959: [60, 8],
    1960: [61, 0], 1961: [61, 4], 1962: [61, 8], 1963: [62, 0],
    1964: [62, 4], 1965: [62, 8],
}

const CHILD_REDUCTION = { 0: 0, 1: 4, 2: 8, 3: 12, 4: 16 }

function byFormula(birthYear) {
    if (birthYear > 1988) return { years: 67, months: 0 }
    const total = 65 * 12 + 8 + (birthYear - 1973)
    return { years: Math.floor(total / 12), months: total % 12 }
}

function subtractMonths({ years, months }, reduction) {
    const total = Math.max(years * 12 + months - reduction, 53 * 12)
    return { years: Math.floor(total / 12), months: total % 12 }
}

export function calcRetirementAge(birthYear, gender, children) {
    if (gender === 'M') {
        if (MALE_TABLE[birthYear]) {
            const [y, m] = MALE_TABLE[birthYear]
            return { years: y, months: m }
        }
        if (birthYear >= 1974 && birthYear <= 1988) return byFormula(birthYear)
        return { years: 67, months: 0 }
    }

    const childCap = Math.min(children, 4)
    const reduction = CHILD_REDUCTION[childCap] ?? 16

    if (FEMALE_TABLE_0[birthYear]) {
        const [y, m] = FEMALE_TABLE_0[birthYear]
        return subtractMonths({ years: y, months: m }, reduction)
    }

    if (birthYear >= 1966 && birthYear <= 1973) {
        const total = 62 * 12 + 8 + (birthYear - 1965) * 4
        const base = { years: Math.floor(total / 12), months: total % 12 }
        return subtractMonths(base, reduction)
    }

    if (birthYear >= 1974 && birthYear <= 1988) return byFormula(birthYear)

    return { years: 67, months: 0 }
}

export function calcRetirementDate(birthDate, gender, children) {
    const { years, months } = calcRetirementAge(birthDate.getFullYear(), gender, children)
    const result = new Date(birthDate)
    result.setFullYear(result.getFullYear() + years)
    result.setMonth(result.getMonth() + months)
    return result
}
