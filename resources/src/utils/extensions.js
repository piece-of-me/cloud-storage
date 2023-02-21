import configImgUrl from 'svg/extensions/config.svg';
import excelImgUrl from 'svg/extensions/excel.svg';
import fileImgUrl from 'svg/extensions/file.svg';
import jsonImgUrl from 'svg/extensions/json.svg';
import pdfImgUrl from 'svg/extensions/pdf.svg';
import powerPointImgUrl from 'svg/extensions/powerpoint.svg';
import textImgUrl from 'svg/extensions/text.svg';
import videoImgUrl from 'svg/extensions/video.svg';
import wordImgUrl from 'svg/extensions/word.svg';
import zipImgUrl from 'svg/extensions/zip.svg';

export function getExtensionImage(extension) {
    if (extension !== null) {
        if (['bat', 'config'].includes(extension)) {
            return configImgUrl;
        } else if (['xls', 'xlsx'].includes(extension)) {
            return excelImgUrl;
        } else if (['json'].includes(extension)) {
            return jsonImgUrl;
        } else if (['pdf'].includes(extension)) {
            return pdfImgUrl;
        } else if (['ppt', 'pps'].includes(extension)) {
            return powerPointImgUrl;
        } else if (['txt'].includes(extension)) {
            return textImgUrl;
        } else if (['mp4', 'avi', 'flv', '3gp', 'webm'].includes(extension)) {
            return videoImgUrl;
        } else if (['doc', 'docx'].includes(extension)) {
            return wordImgUrl;
        } else if (['zip', 'rar'].includes(extension)) {
            return zipImgUrl;
        }
    }
    return fileImgUrl;
}