#!/usr/bin/python
# coding=utf8
# the above tag defines encoding for this document and is for Python 2.x compatibility

from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time
import re

def content_redirect(content):
    regex = r"location\.href\s*=\s*[\"'](https?:\/\/.*)[\"']"
    try:
        result = re.findall(regex,content, re.IGNORECASE)[0]
    except Exception, IndexError:
        regex2 = r"location\s*=\s*[\"'](https?:\/\/.*)[\"']"
        try:
            result = re.findall(regex2,content, re.IGNORECASE)[0]
        except Exception, IndexError:
            result = None

    return result

def new_redirect(content):
    regex = r"meta\s*http-equiv\s*=[\"']?refresh[\"']?\s*content=[\"']?\d*;?url=[\"\']?(https?:\/\/.*)['\"]"
    try:
        result = re.findall(regex,content, re.IGNORECASE)[0]
    except Exception, IndexError:
        result = None
    return result

def page_has_loaded(driver):
    page_state = driver.execute_script('return document.readyState;')
    return page_state == 'complete'

def load(url):
    dcap = dict(DesiredCapabilities.PHANTOMJS)
    dcap["phantomjs.page.settings.userAgent"] = (
        "#AGENT#"
    )
    dcap["javascriptEnabled"] = True
    service_args = [
                     '--proxy=162.243.173.214:22225',
                     '--proxy-auth=#USERNAME#:99oah6sz26i5',
                     '--proxy-type=sock5',
                     ]
    driver = webdriver.PhantomJS(executable_path=r'/usr/local/share/phantomjs-2.1.1-linux-x86_64/bin/phantomjs',desired_capabilities=dcap, service_args=service_args)
    driver.get(url)
    source = None
    if page_has_loaded(driver):
       last_url = driver.current_url
       source = driver.page_source
       redirect = new_redirect(source)
       driver.quit()
       if redirect is not None:
          load(redirect)
       else:
          print(last_url)


load('#URL#')