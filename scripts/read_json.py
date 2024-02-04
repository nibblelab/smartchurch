#!/usr/bin/python3
# -*- coding: utf-8 -*-

import json
from pprint import pprint

class ReadJSON:

    data = None
    
    def load(self, file_json):
        try:
            with open(file_json) as data_file:    
                self.data = json.load(data_file)
        except IOError:
            print('File not found!')
    
    def getData(self):
        return self.data
    
    def __init__(self):
        pass
