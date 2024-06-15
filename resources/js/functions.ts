export function convertJsonToReadableText(jsonText): string
{

    const errors: { [key: string]: string } = JSON.parse(JSON.stringify(jsonText));

    let errorMessage = "";
    for (const key in errors) {
        if (Object.prototype.hasOwnProperty.call(errors, key)) {
            //errorMessage += `${key}: ${errors[key]}\n`;
            errorMessage += `${errors[key]}\n`;
        }
    }

    return errorMessage;
}

export function isValueHours(value): boolean
{
    return (value % 24) > 0;
};
